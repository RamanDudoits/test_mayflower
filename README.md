
# Руководство по развёртыванию Проекта в Docker

## 1. Краткое описание

Это Laravel-проект, работающий в Docker-контейнерах. После развертывания вам нужно:

- Настроить и скопировать файл окружения `.env`.
- Установить зависимости (через контейнер или на хосте при необходимости).
- Запустить контейнеры.
- Выполнить миграции базы данных.
---

## 2. Развёртывание на Linux (Ubuntu)

### 2.1 Установить Docker и Docker Compose

```bash
sudo apt-get update
sudo apt-get install -y ca-certificates curl gnupg lsb-release

curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

echo   "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg]   https://download.docker.com/linux/ubuntu   $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
```

Примечание: Убедитесь, что Docker установлен корректно, выполнив:

```bash
docker --version
```

### 2.2 Скачайте zip-архив с проектом и распакуйте его в нужную папку

```bash
unzip project.zip -d project
cd project
```

### 2.3 Создать файл .env

```bash
cp .env.example .env
# Открываем .env и меняем DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD, и т.д. (при необходимости)
```

### 2.4 Запустить Docker-контейнеры

```bash
docker-compose up -d
```

### 2.5 Установка зависимостей

```bash
docker-compose exec app composer install
```

### 2.6 Выполнить миграции

```bash
docker-compose exec app php artisan migrate
```

---

## 3. Развёртывание на Windows

### 3.1 Установка Docker Desktop, WSL и Hyper-V

**Установить WSL (Windows Subsystem for Linux):**

```powershell
wsl --install
```

Перезагрузите компьютер.

**Включить Hyper-V:**

```powershell
dism.exe /online /enable-feature /featurename:Microsoft-Hyper-V /all /limitaccess /norestart
```

**Включение Hyper-V через Графический интерфейс Windows:**

- Откройте Панель управления (Win + R → введите appwiz.cpl → нажмите Enter).
- Выберите "Программы"
- Перейдите в Включение или отключение компонентов Windows (слева в меню).
- Найдите и установите флажок напротив Hyper-V.
- Нажмите ОК и дождитесь установки.
- Перезагрузите компьютер.


Перезагрузите компьютер.

**Или если у вас домашняя версия**

- Сделайте те же шаги
- включите "Подсистема Windows для Linux"
  ![img.png](imgs_for_docs/img.png)

## Полезные команды для wsl

### Включение WSL
wsl --install

### Проверка установленных дистрибутивов
wsl --list --verbose

### Установка Ubuntu
wsl --install -d Ubuntu

### Установка через Microsoft Store:
- 1. Открыть Microsoft Store
- 2. Найти "Ubuntu" или "Debian"
- 3. Нажать "Установить"

### Установка WSL 2 (если не установлено)
wsl --set-default-version 2
wsl --update

### Запуск WSL
wsl

### Проверка всех установленных дистрибутивов
wsl --list --verbose

### Переключение версии WSL
wsl --set-version Ubuntu 2

### Перезапуск WSL
wsl --shutdown

### Удаление WSL (если нужно)
wsl --unregister Ubuntu


**Установить Docker Desktop**

1. Скачайте и установите последнюю версию Docker Desktop для Windows.
2. В настройках включите:
    - "Use WSL 2 based engine"
    - "Enable integration with my default WSL distro"

### 3.2 Запуск проекта

```bash
unzip project.zip -d project
cd project
cp .env.example .env
```

**Запустить контейнеры:**

```bash
docker-compose up -d
```

**Установка зависимостей:**

```bash
docker-compose exec app composer install
```

**Выполнить миграции:**

```bash
docker-compose exec app php artisan migrate
```

Откройте в браузере `http://localhost:8000/api/v1/stats`.

---

## 4. Дополнительные советы

**Проверка состояния контейнеров:**

```bash
docker-compose ps
docker ps
```

**Остановка контейнеров:**

```bash
docker-compose down
```

**Пересборка контейнеров:**

```bash
docker-compose build
docker-compose up -d
```

**Настройка `.env` файла:**

- Убедитесь, что корректно указаны переменные `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
- Их можно найти в `docker-compose.yml`
- Если порт или пути для приложений отличаются, укажите их в `.env`.

---

### 5. Запуск тестов и нагрузочного бенчмарка

#### 5.1 Запуск автоматических тестов

```bash
docker-compose exec app php artisan test
```

#### 5.2 Проведение нагрузочного тестирования (бенчмаркинга)

Для бенчмаркинга используем утилиту wrk.

Пример: Тестирование POST-запроса на инкремент посещений

1. Создайте файл increment.lua со следующим содержимым:
```lua
wrk.method = "POST"
wrk.body   = '{"country":"ru"}'
wrk.headers["Content-Type"] = "application/json"
```
2. Запустите тестирование (замените порт и адрес при необходимости):
```bash
wrk -t8 -c256 -d30s --latency -s scripts/increment.lua http://localhost:8000/api/v1/visit
```

- t8 — 8 потоков
- c256 — 256 одновременных соединений
- d30s — 30 секунд теста

Результаты тестирования.

```markdown
Running 30s test @ http://localhost:8000/api/v1/visit
  8 threads and 256 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency   104.29ms  144.53ms   1.30s    93.81%
    Req/Sec   439.78    103.55     0.88k    71.08%
  Latency Distribution
     50%   68.12ms
     75%   85.19ms
     90%  142.57ms
     99%  934.55ms
  105691 requests in 31.83s, 24.29MB read
Requests/sec:   3320.80
Transfer/sec:    781.56KB
```

***disclaimer***
#### Это руководство подразумевает что вы работали с Linux
