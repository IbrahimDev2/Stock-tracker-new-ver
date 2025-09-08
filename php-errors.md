# PHP Errors Guide

A detailed guide about PHP errors, their meanings, causes, and fixes.

---

## 1. PHP Error Types – The Basics

| Level          | Meaning                                          | Example                                                      | Code Stops? |
| -------------- | ------------------------------------------------ | ------------------------------------------------------------ | ----------- |
| Notice         | Minor issue, usually doesn’t break your code     | Using a variable that is not defined: `$x` without `$x = 5;` | No          |
| Warning        | Something went wrong, but code may continue      | `include('file.php');` if file not found                     | No          |
| Fatal Error    | Something critical happened, code stops          | `require('file.php');` if file not found                     | Yes         |
| Parse Error    | Syntax error, PHP cannot understand code         | `if($x = 5` (missing `)` )                                   | Yes         |
| Uncaught Error | PHP 7+ special fatal error for objects/functions | Calling a class that doesn’t exist                           | Yes         |

---

## 2. Examples and Fixes

### Notice 🟢

**Problem:** Minor things, like using a variable before defining it.

```php
echo $name; // $name is not defined
```

**Fix:**

```php
$name = "Ibrahim";
echo $name;
```

### Warning 🟡

**Problem:** Something went wrong, but code continues. Often related to files or function parameters.

```php
include 'missing-file.php';
```

**Fix:**

```php
include __DIR__ . '/../missing-file.php';
```

### Fatal / Uncaught Error 🔴

**Problem:** Critical issue, PHP stops execution.

**Example 1: Missing required file**

```php
require 'missing-file.php';
```

**Fix:**

```php
require __DIR__ . '/../vendor/autoload.php';
```

**Example 2: Class does not exist**

```php
$obj = new NonExistClass();
```

**Fix:**

* Make sure the class exists and is included via `require` or Composer autoloader.

### Parse Error 🔴

**Problem:** Syntax problem, PHP cannot even understand your code.

```php
if(true { echo 'Hi'; } // Missing parenthesis
```

**Fix:**

```php
if(true) { echo 'Hi'; }
```

---

## 3. Quick Rules to Remember

* **Notice** → define variables before using them.
* **Warning** → check file paths and function arguments.
* **Fatal / Uncaught** → ensure required files, functions, classes exist.
* **Parse** → fix syntax mistakes (missing `;`, `{}`, `()`).

---

## 4. How to Approach Any PHP Error

1. **Read the error carefully**

   * First word → severity
   * Line number → location
   * File path → which file caused it

2. **Understand the cause**

   * Missing file → `require` or `include` path wrong
   * Undefined variable → variable not declared
   * Class/function missing → not loaded via autoload or file

3. **Fix it**

   * Correct paths
   * Declare variables
   * Include files/classes properly
   * Correct syntax

4. **Test after fixing**

   * Run the script again
   * Ensure the error disappears

---

## 5. Memory Tricks

* **Traffic light colors** → severity (Notice=Green, Warning=Yellow, Fatal/Parse/Uncaught=Red)
* **Real-life analogies** → Notice=small hint, Warning=careful, Fatal=stop immediately
* **Read first word** → instantly know type and severity
* **Practice examples** → reinforce memory
* **Mini cheat sheet** → quick reference for interviews

---

### Cheat Sheet

| Type             | Quick Fix Idea                  |
| ---------------- | ------------------------------- |
| Notice           | Define variables                |
| Warning          | Check file/function/path        |
| Fatal / Uncaught | Include files/classes correctly |
| Parse            | Fix syntax                      |

---

**End of PHP Errors Guide**
