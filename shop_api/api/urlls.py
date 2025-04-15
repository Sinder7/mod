from django.urls import path
from .views import RegisterView, LoginView, CategoryListView, ProductListView, ProductDetailView

urlpatterns = [
    path('register/', RegisterView.as_view()),
    path('login/', LoginView.as_view()),
    path('categories/', CategoryListView.as_view()),
    path('categories/<int:category_id>/products', ProductListView.as_view()),
    path('products/<int:products_id>', ProductDetailView.as_view()),
]
