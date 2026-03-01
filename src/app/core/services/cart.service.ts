import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

export interface CartItem {
  id: number;
  name: string;
  price: number;
  image: string;
  quantity: number;
}

@Injectable({
  providedIn: 'root'
})
export class CartService {
  private cart = new BehaviorSubject<CartItem[]>(this.getCartFromStorage());
  public cart$ = this.cart.asObservable();

  constructor() {}

  addItem(item: CartItem): void {
    const currentCart = this.cart.value;
    const existingItem = currentCart.find(i => i.id === item.id);

    if (existingItem) {
      existingItem.quantity += item.quantity;
    } else {
      currentCart.push(item);
    }

    this.saveCartToStorage(currentCart);
    this.cart.next([...currentCart]);
  }

  removeItem(itemId: number): void {
    const currentCart = this.cart.value.filter(i => i.id !== itemId);
    this.saveCartToStorage(currentCart);
    this.cart.next(currentCart);
  }

  updateQuantity(itemId: number, quantity: number): void {
    const currentCart = this.cart.value;
    const item = currentCart.find(i => i.id === itemId);

    if (item) {
      if (quantity > 0) {
        item.quantity = quantity;
      } else {
        this.removeItem(itemId);
        return;
      }
    }

    this.saveCartToStorage(currentCart);
    this.cart.next([...currentCart]);
  }

  getCart(): CartItem[] {
    return this.cart.value;
  }

  clearCart(): void {
    localStorage.removeItem('cart');
    this.cart.next([]);
  }

  getCartTotal(): number {
    return this.cart.value.reduce((total, item) => total + (item.price * item.quantity), 0);
  }

  getCartItemsCount(): number {
    return this.cart.value.reduce((count, item) => count + item.quantity, 0);
  }

  private saveCartToStorage(cart: CartItem[]): void {
    localStorage.setItem('cart', JSON.stringify(cart));
  }

  private getCartFromStorage(): CartItem[] {
    const cart = localStorage.getItem('cart');
    return cart ? JSON.parse(cart) : [];
  }
}
