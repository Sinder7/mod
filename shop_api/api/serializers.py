from rest_framework import serializers
from django.contrib.auth import authenticate

from .utils import generate_token
from .models import User, Category, Product, Order


class Registerserializer(serializers.ModelSerializer):
    class Meta:
        model = User
        fields = ["email", "password"]
        extra_kwargs = {"password": {"write_only": True}}

    def create(self, validated_data):
        return User.objects.create_user(**validated_data)


class LoginSerializer(serializers.Serializer):
    email = serializers.EmailField()
    password = serializers.CharField(write_only=True)

    def validate(self, data):
        user = authenticate(**data)
        if not user:
            raise serializers.ValidationError("Invalid blyat")
        token = generate_token(user_id=user.id)
        return {"token": token}


class CategorySerializer(serializers.ModelSerializer):
    name = serializers.CharField(source="title")

    class Meta:
        model = Category
        fields = ["id", "name", "description"]

class ProductSerializer(serializers.ModelSerializer):
    image_url = serializers.CharField(source="image")

    class Meta:
        model = Product
        fields = ['id', 'name', 'description', 'price', 'image_url']

class OrderSerializer(serializers.ModelSerializer):
    product = ProductSerializer(read_only=True)

    class Meta:
        model = Order
        fields = ['id', 'status', 'product', 'created_at']