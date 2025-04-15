from rest_framework.authentication import BaseAuthentication
from rest_framework.exceptions import AuthenticationFailed

from .utils import decode_token
from .models import User

class TokenAuthentication(BaseAuthentication):
    def authenticate(self, request):
        token = request.headers.get('Authorization')
        if not token:
            return None

        token = token.replace("Token ", "")
        user_id = decode_token(token)

        if not user_id:
            raise AuthenticationFailed("Invalid token")

        try:
            user = User.objects.get(id=user_id)
        except:
            raise AuthenticationFailed("User not found")

        return (user, None)