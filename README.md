# Pivotal/Analytics/README.md

# Pivotal Analytics Extension

## Overview
The Pivotal Analytics extension for Magento 2 provides advanced analytics capabilities to enhance your e-commerce platform. This extension allows you to track user behavior, analyze sales data, and generate insightful reports to improve your business decisions.

## Installation Instructions

Composer config:

#### Run below commands:

```
composer config repositories.pivotal composer https://gitlab.pivotal.digital/api/v4/group/112/-/packages/composer/packages.json
```

Save access token
```
composer config gitlab-token.gitlab.pivotal.digital <personal_access_token>
```

Replace `<personal_access_token>` with valid token.

1. **Download the Extension**: Clone or download the Pivotal Analytics extension from the repository.
2. **Copy to Magento Root**: Place the `Pivotal` folder in the `app/code` directory of your Magento installation.
3. **Enable the Module**: Run the following command in your Magento root directory:
   ```
   php bin/magento module:enable Pivotal_Analytics
   ```
4. **Run Setup Upgrade**: Execute the setup upgrade command:
   ```
   php bin/magento setup:upgrade
   ```
5. **Deploy Static Content** (if in production mode):
   ```
   php bin/magento setup:static-content:deploy
   ```
6. **Clear Cache**:
   ```
   php bin/magento cache:clean
   php bin/magento cache:flush
   ```

## Usage Guidelines
After installation, you can configure the Pivotal Analytics extension from the Magento admin panel. Navigate to `Stores > Configuration > Pivotal > Analytics` to set up your preferences.

## Features
- User behavior tracking
- Sales data analysis
- Custom report generation

## Support
For any issues or feature requests, please open an issue in the repository or contact our support team.

## License
This extension is licensed under the [Your License Here]. Please refer to the license file for more details.
