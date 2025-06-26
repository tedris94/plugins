# WordPress.org CI/CD Setup Guide

This guide will help you set up automated deployment to WordPress.org plugin repository using GitHub Actions.

## 🚀 Quick Start

### 1. Prerequisites

Before setting up CI/CD, ensure you have:

- [ ] WordPress.org developer account
- [ ] Your plugins approved and added to WordPress.org repository
- [ ] SVN access credentials for WordPress.org
- [ ] GitHub repository with your plugins

### 2. Required GitHub Secrets

You need to add these secrets to your GitHub repository:

**Go to: Repository Settings → Secrets and Variables → Actions**

| Secret Name | Description | Example |
|-------------|-------------|---------|
| `WORDPRESS_ORG_USERNAME` | Your WordPress.org username | `your-wp-username` |
| `WORDPRESS_ORG_PASSWORD` | Your WordPress.org password/app password | `your-secure-password` |
| `SLACK_WEBHOOK_URL` | (Optional) Slack webhook for notifications | `https://hooks.slack.com/...` |

### 3. WordPress.org Plugin Submission Process

#### Initial Submission (Manual)

1. **Submit Plugin to WordPress.org**
   ```bash
   # Go to WordPress.org plugin submission page
   https://wordpress.org/plugins/developers/add/
   ```

2. **Provide Required Information:**
   - Plugin name
   - Plugin description
   - Plugin zip file
   - Wait for approval (can take 1-14 days)

3. **After Approval:**
   - You'll receive SVN repository access
   - Repository URL: `https://plugins.svn.wordpress.org/your-plugin-name/`

#### Automated Deployment (After Approval)

Once your plugin is approved and you have SVN access, the GitHub Actions will handle deployments.

## 📋 Available Workflows

### 1. Validation Workflow (`validate-plugins.yml`)

**Triggers:**
- Push to main branch (plugin files changed)
- Pull requests
- Manual trigger

**What it does:**
- Tests on PHP 7.4, 8.0, 8.1, 8.2
- WordPress Coding Standards check
- Security scanning
- Plugin compliance validation
- Archive creation test

### 2. Deployment Workflow (`deploy-to-wordpress-org.yml`)

**Triggers:**
- Manual deployment via GitHub Actions UI
- Release creation (automatic)

**What it does:**
- Deploys to WordPress.org SVN repository
- Creates version tags
- Cleans up development files
- Notifies deployment status

## 🎯 How to Deploy

### Method 1: Manual Deployment

1. **Go to GitHub Actions tab**
2. **Select "Deploy to WordPress.org" workflow**
3. **Click "Run workflow"**
4. **Fill in the form:**
   - **Plugin name:** `educare-custom-template` or `educare-results-emailer`
   - **Version tag:** `1.0.0` (follow semantic versioning)
5. **Click "Run workflow"**

### Method 2: Automatic via Release

1. **Create a new release on GitHub**
   ```bash
   # Tag format examples:
   educare-custom-template-v1.0.0
   educare-results-emailer-v1.1.0
   ```

2. **The workflow will automatically:**
   - Detect which plugin to deploy based on tag name
   - Deploy to WordPress.org
   - Create SVN tag

## 📝 Plugin Versioning

### Version Number Format
Use semantic versioning: `MAJOR.MINOR.PATCH`

- **MAJOR:** Breaking changes
- **MINOR:** New features (backward compatible)
- **PATCH:** Bug fixes (backward compatible)

### Update Plugin Version

1. **Update version in main plugin file:**
   ```php
   /**
    * Plugin Name: Educare Custom Template
    * Version: 1.0.1
    */
   ```

2. **Update version in readme.txt:**
   ```
   Stable tag: 1.0.1
   ```

3. **Add changelog entry:**
   ```
   == Changelog ==

   = 1.0.1 =
   * Fixed: Class statistics calculation issue
   * Improved: PDF generation performance
   ```

## 🔧 Workflow Customization

### Adding More Validation Checks

Edit `.github/workflows/validate-plugins.yml` to add:

```yaml
- name: Custom Plugin Check
  run: |
    # Your custom validation logic
    echo "Running custom checks..."
```

### Modifying Deployment

Edit `.github/workflows/deploy-to-wordpress-org.yml` to:

- Change file exclusions
- Add pre/post deployment hooks
- Modify notification settings

## 📊 Monitoring

### Check Workflow Status

1. **GitHub Actions Tab:** Monitor workflow runs
2. **Artifacts:** Download compliance reports
3. **Notifications:** Set up Slack integration

### Debugging Failed Deployments

1. **Check workflow logs**
2. **Verify SVN credentials**
3. **Ensure plugin is approved on WordPress.org**
4. **Check WordPress.org plugin page for errors**

## 🚨 Common Issues & Solutions

### Issue: SVN Authentication Failed
**Solution:** 
- Verify `WORDPRESS_ORG_USERNAME` and `WORDPRESS_ORG_PASSWORD` secrets
- Use app-specific password if 2FA is enabled

### Issue: Plugin Not Found on WordPress.org
**Solution:**
- Ensure plugin is approved and exists on WordPress.org
- Check plugin slug matches exactly

### Issue: Version Already Exists
**Solution:**
- Increment version number
- Check existing tags on WordPress.org SVN

### Issue: Files Too Large
**Solution:**
- Review `.svnignore` patterns
- Remove unnecessary files before deployment
- Optimize assets

## 📈 Best Practices

### 1. Testing
- Always run validation workflow before deploying
- Test on multiple PHP versions
- Validate on fresh WordPress installation

### 2. Version Control
- Tag releases properly
- Maintain clear changelog
- Follow semantic versioning

### 3. Security
- Never commit passwords to repository
- Use GitHub secrets for sensitive data
- Regularly update dependencies

### 4. Documentation
- Keep readme.txt updated
- Document new features
- Provide clear installation instructions

## 🎯 Next Steps

1. **Set up GitHub secrets** (required)
2. **Submit plugins to WordPress.org** (if not done)
3. **Test validation workflow**
4. **Make first automated deployment**
5. **Monitor and iterate**

## 📞 Support

- **WordPress.org Plugin Review:** https://wordpress.org/support/forum/plugins-and-hacks/
- **GitHub Actions Documentation:** https://docs.github.com/en/actions
- **SVN Documentation:** https://subversion.apache.org/docs/

---

**Note:** This CI/CD setup assumes your plugins have been approved by WordPress.org. The initial submission still needs to be done manually through the WordPress.org plugin submission process. 