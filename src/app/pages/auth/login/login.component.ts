import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink, Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  template: `
    <div class="login-container">
      <div class="login-card">
        <div class="login-header">
          <i class="fas fa-microchip"></i>
          <h2>Tech Hub</h2>
          <p>Connexion</p>
        </div>

        <form (ngSubmit)="login()" *ngIf="!resetMode">
          <div class="form-group">
            <label for="email">Email</label>
            <input 
              type="email" 
              id="email"
              [(ngModel)]="email" 
              name="email"
              class="form-control" 
              placeholder="Entrez votre email"
              required>
          </div>

          <div class="form-group">
            <label for="password">Mot de passe</label>
            <input 
              type="password" 
              id="password"
              [(ngModel)]="password" 
              name="password"
              class="form-control" 
              placeholder="Entrez votre mot de passe"
              required>
          </div>

          <button type="submit" class="btn btn-primary btn-lg w-100" [disabled]="loading">
            <span *ngIf="!loading"><i class="fas fa-sign-in-alt"></i> Connexion</span>
            <span *ngIf="loading">
              <span class="spinner-border spinner-border-sm me-2"></span>Chargement...
            </span>
          </button>

          <button type="button" (click)="toggleReset()" class="btn btn-link btn-block">
            Mot de passe oublié?
          </button>
        </form>

        <!-- Forgot Password Form -->
        <form (ngSubmit)="forgotPassword()" *ngIf="resetMode">
          <div class="form-group">
            <label for="reset-email">Email</label>
            <input 
              type="email" 
              id="reset-email"
              [(ngModel)]="resetEmail" 
              name="resetEmail"
              class="form-control" 
              placeholder="Entrez votre email"
              required>
          </div>

          <button type="submit" class="btn btn-primary btn-lg w-100" [disabled]="loading">
            <span *ngIf="!loading"><i class="fas fa-redo"></i> Envoyer le code</span>
            <span *ngIf="loading">Chargement...</span>
          </button>

          <button type="button" (click)="toggleReset()" class="btn btn-link btn-block">
            Retour à la connexion
          </button>
        </form>

        <div *ngIf="error" class="alert alert-danger mt-3">
          {{ error }}
        </div>

        <div *ngIf="success" class="alert alert-success mt-3">
          {{ success }}
        </div>

        <div class="login-footer">
          <p>Pas encore de compte? <a routerLink="/register">Créer un compte</a></p>
        </div>
      </div>
    </div>
  `,
  styleUrl: './login.component.scss'
})
export class LoginComponent {
  email = '';
  password = '';
  resetEmail = '';
  resetMode = false;
  loading = false;
  error = '';
  success = '';

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  login(): void {
    if (!this.email || !this.password) {
      this.error = 'Veuillez remplir tous les champs';
      return;
    }

    this.loading = true;
    this.error = '';

    this.authService.login(this.email, this.password).subscribe({
      next: () => {
        this.loading = false;
        this.router.navigate(['/profile']);
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.message || 'Erreur lors de la connexion';
      }
    });
  }

  forgotPassword(): void {
    if (!this.resetEmail) {
      this.error = 'Veuillez entrer votre email';
      return;
    }

    this.loading = true;
    this.error = '';

    this.authService.forgotPassword(this.resetEmail).subscribe({
      next: () => {
        this.loading = false;
        this.success = 'Code de réinitialisation envoyé à votre email';
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.message || 'Une erreur s\'est produite';
      }
    });
  }

  toggleReset(): void {
    this.resetMode = !this.resetMode;
    this.error = '';
    this.success = '';
  }
}
