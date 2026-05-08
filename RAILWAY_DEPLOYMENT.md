# Railway Deployment Guide for Elsda Fish

This project is now ready for deployment to Railway. Follow these steps to deploy:

## Prerequisites
- A Railway account (https://railway.app)
- Your project pushed to GitHub

## Deployment Steps

### 1. Connect GitHub Repository
1. Go to Railway Dashboard
2. Click "Create New Project"
3. Select "GitHub Repo" and authorize Railway
4. Select your `elsda-fish` repository
5. Railway will detect the PHP + Laravel project automatically

### 2. Configure Environment Variables
In Railway dashboard, add these environment variables:

```
APP_NAME=Elsda Fish
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app

DB_CONNECTION=pgsql
DB_HOST=${{POSTGRES.PGHOST}}
DB_PORT=${{POSTGRES.PGPORT}}
DB_DATABASE=${{POSTGRES.PGDATABASE}}
DB_USERNAME=${{POSTGRES.PGUSER}}
DB_PASSWORD=${{POSTGRES.PGPASSWORD}}

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 3. Add PostgreSQL Database
1. In Railway dashboard, click "+ Add Service"
2. Select "PostgreSQL"
3. The variables will be automatically injected as `${{POSTGRES.PGHOST}}`, etc.

### 4. Generate APP_KEY
If you don't have an APP_KEY:
1. Locally, run: `php artisan key:generate`
2. Copy the value from `.env` (remove "base64:" prefix if using SQLite, but Railway uses PostgreSQL)
3. Or run after deployment: `railway run php artisan key:generate`

### 5. Automatic Deployment
- Railway will automatically deploy when you push to your main branch
- The build process will:
  - Install Composer dependencies
  - Run `php artisan migrate --force` (automatic migrations)
  - Install npm packages
  - Build Vite assets

### 6. Manual Deployment (if needed)
```bash
# To manually run artisan commands
railway run php artisan migrate --fresh --seed

# To check logs
railway logs
```

## Important Notes

### Database Migrations
- All migrations are automatically run during deployment via the Procfile
- The database has been fixed to work with PostgreSQL (ENUM replaced with strings)
- All migrations have proper up() and down() methods

### Seeders
- Database seeder will run automatically
- Test user only created in local environment
- Admin user: `admin@elsda.com` / `admin1234` (change in production!)

### Build Process
- Node.js dependencies installed
- Vite compiles assets to `public/build/`
- Apache runs the Laravel app

## Issues Fixed for Railway Deployment

✅ Removed duplicate migrations  
✅ Fixed ENUM fields to strings (PostgreSQL compatibility)  
✅ Added missing foreign key constraints  
✅ Implemented proper down() methods in migrations  
✅ Added return type hints to migrations  
✅ Fixed seeder imports and conditions  
✅ Created Procfile for web server startup  
✅ Created railway.json with build and deploy config  
✅ Updated .env.example for PostgreSQL  

## Troubleshooting

### Build Fails
- Check logs: `railway logs --environment production`
- Ensure composer.json and package.json are committed

### Database Connection Fails
- Verify PostgreSQL service is added
- Check that environment variables are properly set
- Ensure DB_CONNECTION=pgsql (not sqlite)

### Assets Not Loading
- Verify npm build completed: check `public/build/manifest.json` exists
- Clear cache: `railway run php artisan config:clear`

### Migrations Won't Run
- Check logs: `railway run php artisan migrate --verbose`
- Ensure migrations table exists
- Run manually: `railway run php artisan migrate --force`

## Local Testing Before Deployment

```bash
# Set up local environment
cp .env.example .env
php artisan key:generate

# Using PostgreSQL locally (recommended)
# Update .env to use PostgreSQL connection
php artisan migrate:fresh --seed

# Test the application
php artisan serve
```

## Production Checklist

- [ ] APP_KEY generated and set
- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] APP_URL set correctly
- [ ] Database credentials verified
- [ ] Seeders won't create test data in production
- [ ] All migrations run successfully
- [ ] Assets build without errors
- [ ] HTTPS is enabled by default on Railway

---

For more information, visit: https://docs.railway.app
