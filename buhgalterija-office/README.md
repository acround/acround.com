# Buhgalterija
## Замечания по установке
1. Первой миграцией должна быть
```bash
docker exec -it php-site php yii migrate --migrationPath=@yii/rbac/migrations
```
