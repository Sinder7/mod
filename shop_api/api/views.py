from rest_framework.views import APIView
from rest_framework.generics import ListAPIView, RetrieveAPIView
from rest_framework.response import Response
from rest_framework.permissions import AllowAny, IsAuthenticated
from rest_framework import status

from .serializers import Registerserializer, LoginSerializer, CategorySerializer, ProductSerializer, OrderSerializer
from .models import Category, Product, Order


class RegisterView(APIView):
    permission_classes = [AllowAny]

    def post(self, request):
        serializer = Registerserializer(data=request.data)
        if not serializer.is_valid():
            return Response(
                {
                    "message": "Invalid fields",
                    "errors": serializer.errors
                },
                status=status.HTTP_422_UNPROCESSABLE_ENTITY
            )
        user = serializer.save()
        return Response(
            {
                "success": True
            },
            status=status.HTTP_201_CREATED
        )


class LoginView(APIView):
    permission_classes = [AllowAny]

    def post(self, request):
        serializer = LoginSerializer(data=request.data)
        if not serializer.is_valid():
            return Response(
                {
                    "message": "Invalid fields",
                    "errors": serializer.errors
                },
                status=status.HTTP_422_UNPROCESSABLE_ENTITY
            )
        return Response(serializer.validated_data, status=status.HTTP_201_CREATED)


class CategoryListView(ListAPIView):
    queryset = Category.objects.all()
    serializer_class = CategorySerializer

    def list(self, request, *args, **kwargs):
        queryset = self.get_queryset()
        serializer = self.get_serializer(queryset, many=True)
        return Response({"data": serializer.data}, status=200)


class ProductDetailView(RetrieveAPIView):
    queryset = Product.objects.all()
    serializer_class = ProductSerializer

    def retrieve(self, request, *args, **kwargs):
        instance = self.get_object()
        serializer = self.get_serializer(instance)
        return Response(
            {
                "data": serializer.data
            },
            status=status.HTTP_200_OK
        )


class ProductListView(ListAPIView):
    serializer_class = ProductSerializer

    def get_queryset(self):
        category_id = self.kwargs.get("category_id")
        return Product.objects.filter(category_id=category_id)

    def list(self, request, *args, **kwargs):
        queryset = self.get_queryset()
        serializer = self.get_serializer(queryset, many=True)
        return Response({"data": serializer.data}, status=200)


class BuyProductView(APIView):
    permission_classes = [IsAuthenticated]

    def post(self, request, product_id):
        pass
