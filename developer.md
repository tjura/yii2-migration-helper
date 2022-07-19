## Yii 2 - Migration generator

<p style="text-align:center;">
 <a title="PHP Versions Supported"><img alt="PHP Versions Supported" src="https://img.shields.io/badge/php->=8.1-777bb3.svg?logo=php&logoColor=white&labelColor=555555&style=for-the-badge"></a>  
 <a title="PHP Versions Supported"><img alt="" src="https://img.shields.io/badge/Framework-Yii2-777bb3.svg?logo=framework&logoColor=white&labelColor=555555&style=for-the-badge"></a>
</p>

### Install vendors

```bash
docker run --rm -v $(pwd):/app -w /app -it composer composer install
```

### Gitflow

<img src="https://wac-cdn.atlassian.com/dam/jcr:cc0b526e-adb7-4d45-874e-9bcea9898b4a/04%20Hotfix%20branches.svg?cdnVersion=442" alt="gitflow">

 - When release is ready should be merged into master and tagged
 - When hotfix is ready should be merged in to current develop, master and release if exist.

### To do
- Develop the way to standalone testing all features