import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-footer',
  standalone: true,
  imports: [CommonModule],
  template: `
    <footer class="footer">
      <div class="container">
        <div class="row">
          <div class="col-md-4 footer-section">
            <h5><i class="fas fa-microchip"></i> Tech Hub</h5>
            <p>متجر متخصص في بيع معدات الحاسوب الأصلية بأفضل الأسعار والجودة.</p>
            <div class="social-links">
              <a href="#" class="me-2"><i class="fab fa-facebook-f"></i></a>
              <a href="#" class="me-2"><i class="fab fa-twitter"></i></a>
              <a href="#" class="me-2"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
          </div>

          <div class="col-md-4 footer-section">
            <h5>روابط سريعة</h5>
            <ul class="list-unstyled">
              <li><a href="/">الرئيسية</a></li>
              <li><a href="/products">المنتجات</a></li>
              <li><a href="#about">عن الموقع</a></li>
              <li><a href="#contact">تواصل معنا</a></li>
            </ul>
          </div>

          <div class="col-md-4 footer-section">
            <h5>معلومات الاتصال</h5>
            <ul class="list-unstyled">
              <li><i class="fas fa-phone"></i> +966 55 123 4567</li>
              <li><i class="fas fa-envelope"></i> info@techhub.sa</li>
              <li><i class="fas fa-map-marker-alt"></i> الرياض، المملكة العربية السعودية</li>
            </ul>
          </div>
        </div>

        <div class="footer-bottom">
          <p>&copy; 2024 Tech Hub. جميع الحقوق محفوظة.</p>
        </div>
      </div>
    </footer>
  `,
  styleUrl: './footer.component.scss'
})
export class FooterComponent {}
