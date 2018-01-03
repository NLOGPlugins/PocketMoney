PocketMoney
===
새롭게 만들어진 경제 플러그인입니다.
사용법은 비슷하나 Economy API와는 무관합니다.
아직 미발견된 오류가 있을 수 있습니다. 발견시 PR 부탁드립니다.

사용법
---
/money <set|add|take|see|pay|rank>
/money set <player> <amount>
/money take <player> <amount>
/money see <player>
/money pay <player> <amount>
/money rank [page]

개발자들을 위한 사용법
---
```php
$api = PocketMoney::getApi();
```
와 같이 사용하면 됩니다. 메서드는 소스를 참고해 주세요.

안내
---
이 플러그인의 라이선스는 MIT입니다. 이 플러그인으로 무슨 짓을 해도 개발자는 신경쓰지 않습니다.