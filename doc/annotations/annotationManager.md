# 注解管理器

[toc]

imi 中内置了一个注解管理器，通过它可以轻松获取到类、方法、属性、常量中的注解。

类：`\Imi\Bean\Annotation\AnnotationManager`

## 用法

### 获取注解使用点

`AnnotationManager::getAnnotationPoints($annotationClassName, $where = null): array`

| 参数名称 | 说明 |
| ------------ | ------------ 
| annotationClassName | 注解类名 |
| where | 使用点，默认为`null`不限制。可选：`class/method/property/constant` |

**返回：**注解数组

### 获取类注解

`AnnotationManager::getClassAnnotations($className, $annotationClassName = null): array`

| 参数名称 | 说明 |
| ------------ | ------------ 
| className | 目标类名 |
| annotationClassName | 想要获取的注解类类名，为`null`则不限制 |

**返回：**注解数组

### 获取方法注解

`AnnotationManager::getMethodAnnotations($className, $methodName, $annotationClassName = null): array`

| 参数名称 | 说明 |
| ------------ | ------------ 
| className | 目标类名 |
| methodName | 目标方法名 |
| annotationClassName | 想要获取的注解类类名，为`null`则不限制 |

**返回：**注解数组

### 获取属性注解

`AnnotationManager::getPropertyAnnotations($className, $propertyName, $annotationClassName = null): array`

| 参数名称 | 说明 |
| ------------ | ------------ 
| className | 目标类名 |
| propertyName | 目标属性名 |
| annotationClassName | 想要获取的注解类类名，为`null`则不限制 |

**返回：**注解数组

### 获取常量注解

`AnnotationManager::getConstantAnnotations($className, $constantName, $annotationClassName = null): array`

| 参数名称 | 说明 |
| ------------ | ------------ 
| className | 目标类名 |
| constantName | 目标常量名 |
| annotationClassName | 想要获取的注解类类名，为`null`则不限制 |

**返回：**注解数组

### 获取一个类中所有包含指定注解的方法

`AnnotationManager::getMethodsAnnotations($className, $annotationClassName = null): array`

| 参数名称 | 说明 |
| ------------ | ------------ 
| className | 目标类名 |
| annotationClassName | 想要获取的注解类类名，为`null`则不限制 |

**返回：**

```php
[
    '方法名'    =>  [
        // 注解数组
    ],
]
```

### 获取一个类中所有包含指定注解的属性

`AnnotationManager::getPropertiesAnnotations($className, $annotationClassName = null): array`

| 参数名称 | 说明 |
| ------------ | ------------ 
| className | 目标类名 |
| annotationClassName | 想要获取的注解类类名，为`null`则不限制 |

**返回：**

```php
[
    '属性名'    =>  [
        // 注解数组
    ],
]
```

### 获取一个类中所有包含指定注解的常量

`AnnotationManager::getConstantsAnnotations($className, $annotationClassName = null): array`

| 参数名称 | 说明 |
| ------------ | ------------ 
| className | 目标类名 |
| annotationClassName | 想要获取的注解类类名，为`null`则不限制 |

**返回：**

```php
[
    '常量名'    =>  [
        // 注解数组
    ],
]
```
