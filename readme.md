<div align="center">

# 🔍 Laravel FlexSearch

### _Powerful Dynamic Filtering, Relationship & Keyword Search for Laravel Eloquent_

[![Packagist Version](https://img.shields.io/packagist/v/daiyanmozumder/laravel-flexsearch.svg?style=for-the-badge&logo=packagist&logoColor=white)](https://packagist.org/packages/daiyanmozumder/laravel-flexsearch)
[![License](https://img.shields.io/packagist/l/daiyanmozumder/laravel-flexsearch.svg?style=for-the-badge)](LICENSE.md)
[![PHP Version](https://img.shields.io/packagist/php-v/daiyanmozumder/laravel-flexsearch.svg?style=for-the-badge&logo=php&logoColor=white)](https://packagist.org/packages/daiyanmozumder/laravel-flexsearch)
[![Downloads](https://img.shields.io/packagist/dt/daiyanmozumder/laravel-flexsearch.svg?style=for-the-badge)](https://packagist.org/packages/daiyanmozumder/laravel-flexsearch)

**Created with ❤️ by [Daiyan Mozumder](https://github.com/MiltonDaiyan)**

[Features](#features) •
[Installation](#installation) •
[Quick Start](#quick-start) •
[Documentation](#documentation) •
[Examples](#usage-examples) •
[Contributing](#contributing)

---

</div>

## 📑 Table of Contents

-   [✨ Features](#features)
-   [📦 Installation](#installation)
-   [⚡ Quick Start](#quick-start)
-   [🧾 Documentation](#documentation)
-   [🧠 How Keyword Search Works](#how-keyword-search-works)
-   [🔗 Relationship Filtering & Search](#relationship-filtering--search)
-   [💡 Usage Examples](#usage-examples)
-   [🎁 Benefits](#benefits)
-   [🤝 Contributing](#contributing)
-   [📝 License](#license)
-   [🔮 Roadmap](#roadmap)

---

## ✨ Features

<div align="center">

|                                      🎯 **Dynamic Filters**                                      |                                     🔍 **Keyword Search**                                     |                                    🔗 **Relationship Aware**                                     |
| :----------------------------------------------------------------------------------------------: | :-------------------------------------------------------------------------------------------: | :----------------------------------------------------------------------------------------------: |
| Apply simple or operator-based filters (`=`, `>`, `<`, `>=`, `!=`) for flexible database queries | Perform powerful full-text-like search across multiple model columns — even in relationships! | Supports filtering and searching through related models using dot notation (e.g. `company.name`) |

</div>

<br>

<div align="center">

### 🚀 **Zero Configuration Required**

🎉 No service provider or config needed — works instantly out of the box!

</div>

---

## 📦 Installation

Install via Composer:

```bash
composer require daiyanmozumder/laravel-flexsearch
```

> 💡 **No additional setup needed!** The package is ready to use immediately after installation.

---

## ⚡ Quick Start

```php
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request, FlexSearch $flexSearch)
    {
        $query = User::query()->with('company');

        $filters = $request->only(['status', 'company.name']);
        $searchTerm = $request->input('q');
        $searchable = ['name', 'email', 'company.name'];

        $users = $flexSearch->apply($query, $filters, $searchTerm, $searchable)
                            ->paginate(15);

        return view('users.index', compact('users'));
    }
}
```

<div align="center">

✅ **That's it!** You now have dynamic filters, keyword search, and relational querying in one powerful line.

</div>

---

## 🧾 Documentation

### Method Signature

```php
public function apply(
    Builder $query,
    array $filters = [],
    ?string $searchTerm = null,
    array $searchableColumns = []
): Builder
```

### Parameters

| Parameter            | Type      | Required | Description                                       |
| -------------------- | --------- | :------: | ------------------------------------------------- |
| `$query`             | `Builder` |    ✅    | Eloquent query builder instance                   |
| `$filters`           | `array`   |    ❌    | Dynamic key-value filters with optional operators |
| `$searchTerm`        | `?string` |    ❌    | Keyword(s) for text-based search                  |
| `$searchableColumns` | `array`   |    ❌    | Columns (including relation columns) to search    |

### 🧩 Operator-Based Filtering

FlexSearch supports powerful operator-based filtering:

```php
$filters = [
    'price>=' => 100,
    'created_at!=' => '2024-01-01',
    'status' => 'active',
];
```

**Generated SQL:**

```sql
WHERE price >= 100
  AND created_at != '2024-01-01'
  AND status = 'active'
```

---

## 🔗 Relationship Filtering & Search

You can filter or search within related models using **dot notation** for seamless relationship querying.

### 📌 Example 1: Filtering on Relationships

```php
$filters = [
    'company.name=' => 'Ashlar Tech',
    'status' => 'active'
];

$query = User::with('company');
$flexSearch->apply($query, $filters)->get();
```

**Generated SQL (simplified):**

```sql
WHERE EXISTS (
    SELECT * FROM companies
    WHERE users.company_id = companies.id
      AND companies.name = 'Ashlar Tech'
)
AND users.status = 'active'
```

### 🔎 Example 2: Searching on Related Columns

```php
$searchTerm = 'daiyan ashlar';
$searchableColumns = ['name', 'email', 'company.name'];

$query = User::with('company');
$flexSearch->apply($query, [], $searchTerm, $searchableColumns)->get();
```

**Result:** Finds users where `name`, `email`, or `company.name` matches any search term.

---

## 🧠 How Keyword Search Works

FlexSearch splits your input into words, then applies smart logic:

<div align="center">

**AND** between words → _every term must match_  
**OR** between columns → _each term can match any field_

</div>

### Example:

```php
$searchTerm = "red sport";
$columns = ['title', 'description'];
```

**Generated Query:**

```sql
WHERE (
    (title LIKE '%red%' OR description LIKE '%red%')
    AND
    (title LIKE '%sport%' OR description LIKE '%sport%')
)
```

---

## 💡 Usage Examples

### 🛍 Example 1: Product Search

```php
$products = (new FlexSearch())->apply(
    Product::query(),
    ['category_id' => 3, 'price>=' => 100],
    'cotton tshirt',
    ['name', 'description', 'brand.name']
)->get();
```

### 👥 Example 2: User Search with Relationships

```php
$query = User::query()->with('company');

$users = (new FlexSearch())->apply(
    $query,
    ['company.name=' => 'Ashlar Tech', 'status' => 'active'],
    'daiyan',
    ['name', 'email', 'company.name']
)->paginate(10);
```

### 📰 Example 3: Blog Post Search

```php
$posts = (new FlexSearch())->apply(
    Post::with('author', 'tags'),
    ['category_id' => $request->category],
    $request->search,
    ['title', 'body', 'author.name', 'tags.name']
)->paginate(15);
```

---

## 🎁 Benefits

<div align="center">

|         Feature          | Description                                          |
| :----------------------: | :--------------------------------------------------- |
|    🚀 **Zero Setup**     | Works instantly, no config files required            |
|  🔧 **Highly Flexible**  | Handles filters, relations, and keywords with ease   |
| ⚡ **Optimized Queries** | Uses `whereHas` intelligently for better performance |
|  💡 **Readable Syntax**  | Expressive, minimal, and clean API                   |
|   🧱 **ORM Friendly**    | Seamlessly integrates with Eloquent relationships    |
|     🔗 **Chainable**     | Works perfectly with query chains and other builders |

</div>

---

## 🤝 Contributing

We welcome contributions! 🎉

Pull requests are welcome on [GitHub](https://github.com/MiltonDaiyan/laravel-flexsearch).

### Guidelines:

-   ✅ Follow **PSR-12** coding standards
-   ✅ Add tests where applicable
-   ✅ Keep commits meaningful and scoped
-   ✅ Document all new features

<div align="center">

### 🌟 Join our community of contributors!

</div>

---

## 📝 License

The MIT License (MIT). See [LICENSE.md](LICENSE.md) for details.

---

## 🙏 Credits

<div align="center">

**Created & Maintained by**  
**[Daiyan Mozumder](https://github.com/MiltonDaiyan)**

Special thanks to all [contributors](https://github.com/MiltonDaiyan/laravel-flexsearch/graphs/contributors) who help make this project better! 🎉

</div>

---

## 🔮 Roadmap

<div align="center">

| Status | Feature                                         |
| :----: | :---------------------------------------------- |
|   ✅   | Relationship-based search                       |
|   ✅   | Operator-based filtering (`>`, `<`, `>=`, `!=`) |
|   🚧   | `BETWEEN` and `IN` support                      |
|   🚧   | Fuzzy search and match ranking                  |
|   📅   | Result highlighting                             |
|   📅   | Query caching                                   |

</div>

---

<div align="center">

## 💖 Show Your Support

If you find this package helpful, please ⭐ **star it** on [GitHub](https://github.com/MiltonDaiyan/laravel-flexsearch)!

<br>

[![GitHub stars](https://img.shields.io/github/stars/MiltonDaiyan/laravel-flexsearch?style=social)](https://github.com/MiltonDaiyan/laravel-flexsearch/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/MiltonDaiyan/laravel-flexsearch?style=social)](https://github.com/MiltonDaiyan/laravel-flexsearch/network/members)

<br>

---

**Made with ❤️ by [Daiyan Mozumder](https://github.com/MiltonDaiyan)**

_Empowering Laravel developers with flexible search solutions_

---

</div>
