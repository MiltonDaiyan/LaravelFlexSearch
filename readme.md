---

# 🔍 Laravel FlexSearch

<div align="center">

### Powerful Dynamic Filtering & Search for Laravel Eloquent

<a href="https://packagist.org/packages/daiyanmozumder/laravel-flexsearch">
  <img src="https://img.shields.io/packagist/v/daiyanmozumder/laravel-flexsearch.svg" alt="Packagist Version" />
</a>
<a href="LICENSE.md">
  <img src="https://img.shields.io/packagist/l/daiyanmozumder/laravel-flexsearch.svg" alt="License" />
</a>

<img src="[https://avatars.githubusercontent.com/u/108240573?v=4]" width="150" height="150" style="border-radius:50%;" alt="Daiyan Mozumder">

**Created by [Daiyan Mozumder](https://github.com/MiltonDaiyan)**

</div>

---

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Documentation](#documentation)
- [How Keyword Search Works](#how-keyword-search-works)
- [Usage Examples](#usage-examples)
- [Benefits](#benefits)
- [Contributing](#contributing)
- [License](#license)
- [Roadmap](#roadmap)

---

<a id="features"></a>

## ✨ Features

<table>
<tr>
<td width="33%" valign="top">

### 🎯 Dynamic Filters

Apply simple where clause filters based on key-value pairs (e.g., filtering by status, category, or any column).

</td>
<td width="33%" valign="top">

### 🔍 Keyword Search

Perform powerful, full-text-like search across multiple specified model columns effortlessly.

</td>
<td width="33%" valign="top">

### 🧠 Split-Term Search

Automatically splits search terms by spaces and ensures every word is found, providing highly relevant results.

</td>
</tr>
</table>

---

<a id="installation"></a>

## 📦 Installation

Install the package via Composer:

bash
composer require daiyanmozumder/laravel-flexsearch


> *🎉 Note:* This package does not require any service provider registration or configuration files. It works out of the box!

---

<a id="quick-start"></a>

## Quick Start

Here's a simple example to get you started in seconds:

php
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request, FlexSearch $flexSearch)
    {
        // Start with your base query
        $query = Post::query();

        // Define search parameters
        $filters = $request->only(['category_id', 'status']);
        $searchTerm = $request->input('q');
        $searchableColumns = ['title', 'body', 'slug'];

        // Apply flexible search
        $posts = $flexSearch->apply(
            $query,
            $filters,
            $searchTerm,
            $searchableColumns
        )->paginate(15);

        return view('posts.index', compact('posts'));
    }
}


*That's it!* Your search and filtering functionality is ready. 🎊

---

<a id="documentation"></a>

## Documentation

### The apply() Method

The apply() method is the heart of FlexSearch, accepting four parameters:

| Parameter            | Type      | Required | Default | Description                                   |
| -------------------- | --------- | -------- | ------- | --------------------------------------------- |
| $query             | Builder | ✅ Yes   | -       | Your existing Eloquent Query Builder instance |
| $filters           | array   | ❌ No    | []    | Key-value pairs for exact column matches      |
| $searchTerm        | ?string | ❌ No    | null  | The keyword(s) to search for                  |
| $searchableColumns | array   | ❌ No    | []    | Database columns to search within             |

---

### Parameter Details

#### 1. **Builder $query** (Required)

The starting point of your database query. Pass an Eloquent Query Builder instance.

php
// Examples:
Post::query()
User::where('is_active', true)
Product::with('category')


#### 2. **array $filters** (Optional)

Apply direct equality filters. Array keys must match column names.

php
// Example:
$filters = [
    'category_id' => 5,
    'status' => 'published',
    'featured' => true
];

// Generates: WHERE category_id = 5 AND status = 'published' AND featured = 1


#### 3. **?string $searchTerm** (Optional)

The text input for keyword search. Can be null.

php
// Examples:
"Laravel package"
"best article about PHP"
"javascript tutorial"


#### 4. **array $searchableColumns** (Optional)

Database columns to include in the keyword search.

php
// Example:
$searchableColumns = [
    'title',
    'description',
    'tags',
    'author_name'
];


---

<a id="how-keyword-search-works"></a>

## How Keyword Search Works

FlexSearch uses intelligent split-term searching for maximum relevance:

### Search Logic

1. *Split by Spaces:* The search term is divided into individual words
2. *AND Between Terms:* Every word must be found somewhere
3. *OR Across Columns:* Each word can match any searchable column

### Example Query

<table>
<tr>
<td width="50%">

*Input:*

php
$searchTerm = "red sport";
$searchableColumns = ['name', 'description'];


</td>
<td width="50%">

*Generated SQL:*

sql
WHERE (
    (name LIKE '%red%' OR description LIKE '%red%')
    AND
    (name LIKE '%sport%' OR description LIKE '%sport%')
)


</td>
</tr>
</table>

This ensures that results contain *both* "red" *and* "sport" somewhere in the searchable columns, providing highly relevant matches.

---

<a id="usage-examples"></a>

## 💡 Usage Examples

### Example 1: Simple Product Search

php
public function searchProducts(Request $request, FlexSearch $flexSearch)
{
    $products = $flexSearch->apply(
        Product::query(),
        $request->only(['category_id', 'brand']),
        $request->input('search'),
        ['name', 'description', 'sku']
    )->get();

    return response()->json($products);
}


### Example 2: User Search with Relationships

php
public function searchUsers(Request $request, FlexSearch $flexSearch)
{
    $users = $flexSearch->apply(
        User::with('profile'),
        ['role' => 'admin'],
        $request->input('q'),
        ['name', 'email', 'phone']
    )->paginate(20);

    return view('admin.users', compact('users'));
}


### Example 3: Advanced Blog Search

php
public function search(Request $request, FlexSearch $flexSearch)
{
    $query = Post::published()
                 ->with('author', 'tags')
                 ->latest();

    $posts = $flexSearch->apply(
        $query,
        [
            'category_id' => $request->category,
            'featured' => $request->featured
        ],
        $request->search,
        ['title', 'body', 'excerpt', 'meta_description']
    )->paginate(15);

    return view('posts.search', compact('posts'));
}


---

<a id="benefits"></a>

## 🎁 Benefits

| Feature                   | Benefit                           |
| ------------------------- | --------------------------------- |
| 🚀 *Zero Configuration* | Install and use immediately       |
| 🔧 *Highly Flexible*    | Works with any Eloquent model     |
| 📦 *Lightweight*        | Minimal overhead and dependencies |
| 🎯 *Relevant Results*   | Smart split-term search logic     |
| 💡 *Easy to Learn*      | Simple, intuitive API             |
| 🔗 *Chain Compatible*   | Works with existing query chains  |
| ⚡ *Performance*        | Optimized database queries        |
| 🛠 *No Config Files*    | Zero configuration required       |

---

<a id="contributing"></a>

## 🤝 Contributing

Contributions are *welcome* and will be fully *credited*! We accept contributions via Pull Requests on [GitHub](https://github.com/daiyanmozumder/laravel-flexsearch).

### Pull Request Guidelines:

- *PSR-2 Coding Standard* - Check code style with composer check-style and fix with composer fix-style
- *Add tests!* - Your patch won't be accepted if it doesn't have tests
- *Document changes* - Make sure the README and other documentation are up-to-date
- *One pull request per feature* - Send multiple PRs for multiple features
- *Send coherent history* - Make sure commits are logically organized

---

## 🔒 Security

If you discover any security related issues, please email the maintainer instead of using the issue tracker.

---

<a id="license"></a>

## 📝 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

## 🙏 Credits

- *[Daiyan Mozumder](https://github.com/daiyanmozumder)* - Creator & Maintainer
- *[All Contributors](https://github.com/daiyanmozumder/laravel-flexsearch/contributors)* - Thank you!

---

<div align="center">

### 💖 Show Your Support

If you find this package helpful, please consider giving it a ⭐ on [GitHub](https://github.com/daiyanmozumder/laravel-flexsearch)!

*Made with ❤ by [Daiyan Mozumder](https://github.com/daiyanmozumder)*

[Report Bug](https://github.com/daiyanmozumder/laravel-flexsearch/issues) - [Request Feature](https://github.com/daiyanmozumder/laravel-flexsearch/issues)

</div>

---

<a id="roadmap"></a>

## 🔮 Roadmap

This project is a work in progress. Upcoming features include:

- [ ] Advanced filtering operators (>, <, >=, <=, BETWEEN)
- [ ] Relationship search support
- [ ] Custom search scopes
- [ ] Search result highlighting
- [ ] Query caching support
- [ ] Fuzzy search capabilities

---

<div align="center">

*Happy Searching! 🔍✨*

</div>

---
