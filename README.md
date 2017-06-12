# Meat Project
by [Digital Meat](http://meat.cl/)

- [Como hacer empanadas](#Installation)
- [Environments](#environments)

<a name="installation"></a>
## Installation 
You can install the project using `meat-cli`
```bash
composer global require meat/meat-cli
``` 
```bash
cd ~/code/
meat install project
```

### Manual installation
#### Development / Local Environment
```bash
git clone git@bitbucket.org:digitalmeatdev/project.git
cd project
npm install
composer install --prefer-dist
cp .env.example .env
vim .env  
npm run watch
php artisan serve
```

#### Production Environments
```bash
git clone git@bitbucket.org:digitalmeatdev/project.git
cd project
npm install
composer install --prefer-dist --no-dev
npm run production
cp .env.example .env
vim .env  
```

<a name="environments"></a>
## Environments
#### Staging: 
    URL: https://project.dmeat.cl
    
#### Producción:
    URL: https://project.cl
