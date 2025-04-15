from datetime import datetime, timedelta

from django.core import signing

TOKEN_EXPERATION_HOURS = 24

def generate_token(user_id):
    expires_at = (datetime.now() + timedelta(hours=TOKEN_EXPERATION_HOURS)).timestamp()
    value = {
        "user_id": user_id,
        "expires_at": expires_at
    }
    return signing.dumps(value)

def decode_token(token):
    try:
        data = signing.loads(token)
        if data["expires_at"] < datetime.utcnow().timestamp():
            return None
        return data["user_id"]
    except signing.BadSignature:
        return None