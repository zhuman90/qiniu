# laravuel/qiniu
七牛云laravel扩展包

## 安装
```
composer require laravuel/qiniu
```

## 配置
1. 在 config/app.php 注册 ServiceProvider
```
'providers' => [
    // ...
    Laravuel\Qiniu\ServiceProvider::class,
],
```
2. 创建配置文件
```
php artisan vendor:publish --provider="Laravuel\Qiniu\ServiceProvider"
```
3. 修改应用根目录下的 config/qiniu.php 中对应的参数即可。
