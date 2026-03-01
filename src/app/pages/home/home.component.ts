import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterLink],
  template: `
    <div class="hero-section">
      <div class="hero-content">
        <h1>Votre Boutique de Matériel Informatique</h1>
        <p>Composants de qualité aux meilleurs prix des plus grandes marques mondiales</p>
        <a routerLink="/products" class="btn btn-primary btn-lg">
          <i class="fas fa-shopping-bag"></i> Commencer les achats
        </a>
      </div>
    </div>

    <section class="featured-section">
      <div class="container">
        <div class="section-header">
          <h2>Produits Vedettes</h2>
          <p>Nos meilleures sélections de produits</p>
        </div>

        <div class="products-grid">
          <div class="product-card" *ngFor="let product of featuredProducts">
            <div class="product-image">
              <img [src]="product.image || 'https://via.placeholder.com/250x250'" 
                   [alt]="product.name"
                   (error)="onImageError($event)">
              <div class="product-badge" *ngIf="product.discount">
                -{{ product.discount }}%
              </div>
            </div>
            <div class="product-info">
              <h5 class="product-name">{{ product.name }}</h5>
              <p class="product-category" *ngIf="product.category">{{ product.category }}</p>
              
              <div class="product-price">
                <span class="original-price" *ngIf="product.original_price">
                  {{ product.original_price }} €
                </span>
                <span class="current-price">{{ product.price }} €</span>
              </div>

              <div class="product-rating" *ngIf="product.rating">
                <i class="fas fa-star" *ngFor="let i of [1,2,3,4,5]"></i>
                ({{ product.rating }})
              </div>

              <div class="product-actions">
                <a [routerLink]="['/products', product.id]" class="btn btn-outline-primary">
                  <i class="fas fa-eye"></i> Voir les détails
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="categories-section">
      <div class="container">
        <div class="section-header">
          <h2>Catégories</h2>
        </div>
        
        <div class="categories-grid">
          <div class="category-card" *ngFor="let category of categories">
            <div class="category-icon">
              <i [class]="getCategoryIcon(category.name)"></i>
            </div>
            <h5>{{ category.name }}</h5>
            <p>{{ category.description || 'Découvrez les produits' }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="features-section">
      <div class="container">
        <div class="row">
          <div class="col-md-3 feature">
            <div class="feature-icon">
              <i class="fas fa-shipping-fast"></i>
            </div>
            <h5>Livraison Rapide</h5>
            <p>Livraison dans toute la France en 48h</p>
          </div>
          <div class="col-md-3 feature">
            <div class="feature-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h5>Produits Authentiques</h5>
            <p>Garantie 100% sur tous les produits</p>
          </div>
          <div class="col-md-3 feature">
            <div class="feature-icon">
              <i class="fas fa-undo"></i>
            </div>
            <h5>Retours Sécurisés</h5>
            <p>Politique de retour simple et sécurisée</p>
          </div>
          <div class="col-md-3 feature">
            <div class="feature-icon">
              <i class="fas fa-headset"></i>
            </div>
            <h5>Support Client</h5>
            <p>Service client 24/7 pour vous aider</p>
          </div>
        </div>
      </div>
    </section>
  `,
  styleUrl: './home.component.scss'
})
export class HomeComponent implements OnInit {
  featuredProducts: any[] = [];
  categories: any[] = [];

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadFeaturedProducts();
    this.loadCategories();
  }

  loadFeaturedProducts(): void {
    this.apiService.getArticles().subscribe({
      next: (response) => {
        this.featuredProducts = response.slice(0, 8);
      },
      error: (error) => console.error('Error loading products:', error)
    });
  }

  loadCategories(): void {
    this.apiService.getCategories().subscribe({
      next: (response) => {
        this.categories = response.slice(0, 6);
      },
      error: (error) => console.error('Error loading categories:', error)
    });
  }

  getCategoryIcon(categoryName: string): string {
    const iconMap: { [key: string]: string } = {
      'Processeurs': 'fas fa-microchip',
      'Mémoire': 'fas fa-memory',
      'Disques Durs': 'fas fa-hdd',
      'Cartes Graphiques': 'fas fa-video',
      'Alimentations': 'fas fa-power-off',
      'Refroidisseurs': 'fas fa-fan',
      'Accessoires': 'fas fa-laptop'
    };
    return iconMap[categoryName] || 'fas fa-laptop';
  }

  onImageError(event: any): void {
    event.target.src = 'https://via.placeholder.com/250x250?text=No+Image';
  }
}
