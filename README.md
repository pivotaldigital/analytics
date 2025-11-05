# Pivotal/Analytics/README.md

# Pivotal Analytics Extension

## Overview
The Pivotal Analytics extension for Magento 2 provides advanced analytics capabilities to enhance your e-commerce platform. This extension allows you to track user behavior, analyze sales data, and generate insightful reports to improve your business decisions.

## Installation Instructions

### Composer installation:

#### Run below commands:

```
composer require pivotal/analytics
```

### Manual installation:

1. **Download the Extension**: Clone or download the Pivotal Analytics extension from the [repository](https://github.com/pivotaldigital/analytics).
2. **Copy to Magento Root**: Place the `Pivotal` folder in the `app/code` directory of your Magento installation.

## Magento setup commands

1. **Enable the Module**: Run the following command in your Magento root directory:
   ```
   php bin/magento module:enable Pivotal_Analytics
   ```
2. **Run Setup Upgrade**: Execute the setup upgrade command:
   ```
   php bin/magento setup:upgrade
   ```
3. **Deploy Static Content** (if in production mode):
   ```
   php bin/magento setup:static-content:deploy
   ```
4. **Clear Cache**:
   ```
   php bin/magento cache:clean
   php bin/magento cache:flush
   ```

## Usage Guidelines
After installation, you can configure the Pivotal Analytics extension from the Magento admin panel. Navigate to `Stores > Configuration > General > Pivotal Analytics` to set up your preferences.

## Features
- Add to cart event tracking
- View cart activity tracking
- Purchase event tracking
- Refund order event tracking
- Order cancellation event tracking

## Support
For any issues or feature requests, please open an issue in the repository or contact our support team.

## License
This extension is licensed under the Open Software License 3.0 (OSL-3.0).
