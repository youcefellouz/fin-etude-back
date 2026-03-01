import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../core/services/api.service';
import { AuthService } from '../../core/services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-orders',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="container mt-4 mb-5">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
          <li class="breadcrumb-item active">طلباتي</li>
        </ol>
      </nav>

      <h2 class="mb-4"><i class="fas fa-shopping-bag"></i> طلباتي</h2>

      <div *ngIf="orders.length === 0" class="alert alert-info text-center">
        <i class="fas fa-inbox"></i>
        <p>لا توجد طلبات حالياً</p>
        <a href="/products" class="btn btn-primary">ابدأ التسوق</a>
      </div>

      <div *ngFor="let order of orders" class="order-card">
        <div class="order-header">
          <div>
            <h5>الطلب #{{ order.id }}</h5>
            <p class="text-muted">{{ order.created_at | date: 'medium' }}</p>
          </div>
          <div class="order-status" [class]="'status-' + order.status">
            {{ getStatusLabel(order.status) }}
          </div>
        </div>

        <div class="order-items">
          <h6>المنتجات:</h6>
          <table class="items-table">
            <thead>
              <tr>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>السعر</th>
                <th>الإجمالي</th>
              </tr>
            </thead>
            <tbody>
              <tr *ngFor="let item of order.articles">
                <td>{{ item.name }}</td>
                <td>{{ item.pivot.quantity }}</td>
                <td>{{ item.pivot.unit_price }} ر.س</td>
                <td>{{ item.pivot.quantity * item.pivot.unit_price }} ر.س</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="order-footer">
          <div class="order-total">
            <strong>الإجمالي:</strong>
            <strong class="price">{{ order.global_price }} ر.س</strong>
          </div>
          <button (click)="viewOrderDetails(order.id)" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-eye"></i> عرض التفاصيل
          </button>
        </div>
      </div>
    </div>
  `,
  styleUrl: './orders.component.scss'
})
export class OrdersComponent implements OnInit {
  orders: any[] = [];

  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadOrders();
  }

  loadOrders(): void {
    this.apiService.getOrders().subscribe({
      next: (response) => {
        this.orders = response;
      },
      error: (error) => {
        console.error('Error loading orders:', error);
        if (error.status === 401) {
          this.router.navigate(['/login']);
        }
      }
    });
  }

  getStatusLabel(status: string): string {
    const statusLabels: { [key: string]: string } = {
      'pending': 'قيد الانتظار',
      'confirmed': 'مؤكد',
      'shipped': 'تم الشحن',
      'delivered': 'تم التسليم',
      'cancelled': 'ملغى'
    };
    return statusLabels[status] || status;
  }

  viewOrderDetails(orderId: number): void {
    // Implement order details navigation
    alert(`تفاصيل الطلب ${orderId}`);
  }
}
