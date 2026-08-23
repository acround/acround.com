# Buhgalterija
## Замечания по установке
1. Первой миграцией должна быть
```bash
docker exec -it buhgalterija-office php yii migrate --migrationPath=@yii/rbac/migrations
```
Откатить ее нужно, напротив, последней. Команда:
```bash
docker exec -it buhgalterija-office php yii migrate/down 4 --migrationPath=@yii/rbac/migrations
```
