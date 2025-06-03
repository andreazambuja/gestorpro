# 🚀 Gestor Pro

[![Instalação Rápida](https://img.shields.io/badge/Instala%C3%A7%C3%A3o-R%C3%A1pida-green)](https://github.com/)  
Sistema completo para gestão de clientes, envio de mensagens automáticas via WhatsApp, controle de cobranças e administração geral com visual profissional. Fácil instalação com Docker e gerenciamento via Portainer.

---

## 📦 Pré-requisitos
- Evolution api Version: 2.2.3 em diante
- hospedagem cpanel
- Docker opcional
- Portainer configurado e acessível via navegador
- Conexão com a internet
- clone nosso repositorio
- git clone https://github.com/andreazambuja/gestorpro.git
- nosso mini sistema conta com uma api para puxar informações do cliente
- localhost/api_buscar_cliente.php?telefone={{telefone}}- 
- Baixe o banco de dados caso o banco de dados não subir no deploy
- https://drive.google.com/file/d/1m4fJSAJI7gyLDxer4WpgBz4g5PK8LDqA/view?usp=drive_link

---
## 📂 Instalação via cPanel

Suba todos os aquivos para o servidor, edite os arquivos `conexao.php`, `send.php`, `configuracoes.php` com os dados do seu banco de dados.  
Depois, acesse seu phpMyAdmin e importe o banco de dados.  
Acesse seu domínio e entre com as credenciais:

- **Usuário:** `admin@gmail.com`
- **Senha:** `123456`

Se o banco de dados não subir automaticamente, use o link:
- https://drive.google.com/file/d/1m4fJSAJI7gyLDxer4WpgBz4g5PK8LDqA/view?usp=drive_link

---

## 🧱 Instalação via Portainer

### 1. Copie o Docker Compose

Crie uma nova stack no seu Portainer com o seguinte conteúdo:

```yaml
version: "3.9"

services:
  app:
    image: andreazambuja/gestor-crm:1.3
    networks:
      - financeiro
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.financeiro.rule=Host(`painel.seudominio`)"
      - "traefik.http.routers.financeiro.entrypoints=websecure"
      - "traefik.http.routers.financeiro.priority=1"
      - "traefik.http.routers.financeiro.tls.certresolver=leresolver"
      - "traefik.http.services.financeiro.loadbalancer.server.port=80"
    depends_on:
      - db
    restart: unless-stopped

  db:
    image: mysql:5.7
    networks:
      - financeiro
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: admin
      MYSQL_USER: admin
      MYSQL_PASSWORD: admin
    volumes:
      - dbdata:/var/lib/mysql
      - ./init.sql:/docker-entrypoint-initdb.d/init.sql:ro

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    networks:
      - financeiro
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.phpmyadmin.rule=Host(`php.seudominio`)"
      - "traefik.http.routers.phpmyadmin.entrypoints=websecure"
      - "traefik.http.routers.phpmyadmin.priority=1"
      - "traefik.http.routers.phpmyadmin.tls.certresolver=leresolver"
      - "traefik.http.services.phpmyadmin.loadbalancer.server.port=80"
    environment:
      PMA_HOST: db
      PMA_PORT: 3306
      MYSQL_ROOT_PASSWORD: root
    depends_on:
      - db
    restart: unless-stopped

volumes:
  dbdata:

networks:
  financeiro:
    external: true
```

### 2. Acesse o Portainer

Abra seu navegador e vá para:

```
http://localhost:9000
```

1. Faça login.
2. Vá em **Stacks > Add Stack**.
3. Cole o conteúdo acima.
4. Clique em **Deploy the stack**.

---

## 🌐 Acesse o sistema

Após subir os containers, você poderá acessar:

- **Painel do Gestor Pro:** `http://localhost:8080` *(ou seu domínio com Traefik configurado)*
- **phpMyAdmin:** `[http://localhost:8081}`

---

## 🔑 Acesso Inicial

Use as credenciais padrão para login:

- **Usuário:** `admin@gmail.com`  
- **Senha:** `123456`

---

## ⚙️ Funcionalidades

- 📋 Cadastro e gestão de clientes
- 🔔 Mensagens automáticas via WhatsApp
- 💰 Controle de vencimentos e cobranças
- 🌙 Interface profissional com tema escuro
- 🔌 Integrações com Typebot, Chatwoot e outros

---

## 💡 Dicas

- Personalize seu domínio configurando corretamente o Traefik e DNS.
- Altere os dados do banco no `conexao.php` caso utilize outra senha/usuário.
- Use `phpMyAdmin` para gerenciar diretamente o banco de dados, se necessário.

---

## 📄 Licença

Este projeto é licenciado de forma privada para clientes autorizados.

---
