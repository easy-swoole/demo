## File

#### 命名空间地址

EasySwoole\Core\Utility\File

#### 方法列表

创建目录：

- string `dirPath` 目录名

```php
static function createDir($dirPath):bool
```

删除目录：

- string `dirPath` 目录名

```php
static function deleteDir($dirPath):bool
```

清理目录：

- string `dirPath` 目录名

```php
static function clearDir($dirPath):bool
```

复制目录：

- string `dirPath` 目录名
- string `targetPath` 复制到
- bool `overwrite`  是否覆盖

```php
static function copyDir($dirPath,$targetPath,$overwrite = true):bool
```

移动目录：

- string `dirPath` 目录名
- string `targetPath` 复制到
- bool `overwrite`  是否覆盖

```php
static function moveDir($dirPath,$targetPath,$overwrite = true):bool
```

创建文件：

- string `filePath` 文件名
- bool `overwrite`  是否覆盖

```php
static function createFile($filePath, $overwrite = true):bool
```

保存文件：

- string `filePath` 文件名
- string `content` 文件内容
- bool `overwrite`  是否覆盖

```php
static function saveFile($filePath,$content, $overwrite = true):bool
```

复制文件：

- string `filePath` 文件名
- string `targetFilePath` 复制后的文件名
- bool `overwrite`  是否覆盖

```php
static function copyFile($filePath,$targetFilePath,$overwrite = true):bool
```

移动文件：

- string `filePath` 文件名
- string `targetFilePath` 复制后的文件名
- bool `overwrite`  是否覆盖

```php
static function moveFile($filePath,$targetFilePath,$overwrite = true):bool
```

删除文件：

- string `filePath` 文件名

```php
static function deleteFile($filePath):bool
```

获得文件目录或目录文件数组：

- string `dirPath` 目录名
- string `filterType` 过滤类型 TYPE_FILE | TYPE_DIR

```php
static function scanDir($dirPath, $filterType = self::TYPE_FILE):?array
```