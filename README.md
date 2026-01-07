# CloudFlow S3 Uploader

[![Deploy to Render](https://render.com/images/deploy-to-render-button.svg)](https://render.com/deploy?repo=https://github.com/sajidmahamud835/amazon-s3-file-uploader-php)
[![Live Demo](https://img.shields.io/badge/Live-Demo-00C7B7?logo=render&logoColor=white)](https://cloudflow-s3-uploader.onrender.com)
[![PHP 8.2](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

A secure, production-ready file uploader for Amazon S3 with a premium Glassmorphism UI, built for researchers and developers exploring cloud storage integration patterns.

---

## 📚 Academic Context

This project serves as a **reference implementation** for studying:
- **Cloud Storage Patterns**: Direct-to-S3 upload workflows with presigned URLs vs server-side proxy
- **Security Best Practices**: CSRF protection, MIME validation, session-based credential handling
- **Modern PHP Architecture**: PSR-4 autoloading, MVC-like separation, environment-based configuration
- **UX Engineering**: Drag & Drop interfaces, inline validation, and progressive enhancement

---

## ✨ Features

| Category | Details |
|----------|---------|
| **UI/UX** | Glassmorphism design, Drag & Drop, instant previews, responsive layout |
| **Security** | CSRF tokens, MIME type validation, file size limits, no credential storage |
| **Demo Mode** | Users can enter their own AWS credentials when env vars are missing |
| **Architecture** | PSR-4 autoloading, `src/` class structure, template separation |
| **DevOps** | Docker, docker-compose, Render Blueprint (`render.yaml`) |

---

## 🚀 Quick Start

### One-Click Deploy (Render)
Click the badge above. Leave env vars empty for **demo mode**.

### Local (Docker)
```bash
git clone https://github.com/sajidmahamud835/amazon-s3-file-uploader-php.git
cd amazon-s3-file-uploader-php
cp .env.example .env  # Or leave empty for demo mode
docker-compose up -d --build
# Open http://localhost:8080
```

### Local (PHP)
```bash
composer install
cd public && php -S localhost:8000
# Open http://localhost:8000
```

---

## 📁 Technical Architecture

```
amazon-s3-file-uploader-php/
├── src/
│   ├── Config.php         # Environment & session credential handling
│   └── S3Uploader.php     # AWS SDK wrapper with validation
├── public/
│   ├── index.php          # Entry point (controller logic)
│   └── assets/            # CSS (Glassmorphism) & JS (Drag/Drop)
├── templates/
│   ├── layout.php         # Base HTML with animated background
│   ├── upload_form.php    # Main upload UI
│   └── config_form.php    # Demo mode credential input
├── Dockerfile
├── docker-compose.yml
└── render.yaml            # Render Blueprint
```

---

## 📋 Research & Development Plan (Todo)

- [ ] Add AJAX-based upload with real progress bar
- [ ] Implement presigned URL pattern for direct browser-to-S3 uploads
- [ ] Add multi-file upload support
- [ ] Integrate object lifecycle policies display
- [ ] Add download URL generation with expiry

---

## 🔗 Related Projects

- **[EasyCom](https://github.com/sajidmahamud835/easycom)**: E-commerce platform with similar modern UI patterns
- **[BankSync](https://github.com/sajidmahamud835/banksync)**: FinTech app demonstrating secure credential management
- **[Developer Portfolio](https://github.com/sajidmahamud835/developer-portfolio)**: Zero-dependency SPA with custom physics engine

---

## 📜 License

MIT License - See [LICENSE](LICENSE) for details.

*Part of [Sajid Mahamud's Project Portfolio](https://github.com/sajidmahamud835)*
