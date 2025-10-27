# Laravel FlexSearch

A simple and flexible Laravel package to easily apply **dynamic filters** and **keyword search** to your Eloquent models.

---

## ✨ Features

* **Dynamic Filters:** Apply simple `where` clause filters based on key-value pairs (e.g., filtering by status or category).
* **Keyword Search:** Perform a powerful, full-text-like search across multiple specified model columns.
* **Split-Term Search:** Automatically splits the search term by spaces and ensures **every word** is found in at least one searchable column, providing highly relevant results.

---

## 💾 Installation

You can install the package via Composer.

```bash
composer require daiyanmozumder/laravel-flexsearch

```
Note: This package does not require any service provider registration or configuration files.

🚀 Usage
1. Basic Setup
To use the package, you simply inject the FlexSearch class into your method and call the apply method on an existing Eloquent query builder instance.


In your Controller or Service...
```
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request, FlexSearch $flexSearch)
    {
        // 1. Start with your base query
        $query = Post::query();

        // 2. Define the search parameters
        $filters = $request->only(['category_id', 'status']);
        $searchTerm = $request->input('q');
        $searchableColumns = ['title', 'body', 'slug']; // Columns to search across

        // 3. Apply the flexible search
        $posts = $flexSearch->apply(
            $query,
            $filters,
            $searchTerm,
            $searchableColumns
        )->paginate(15);

        return view('posts.index', compact('posts'));
    }
}
```

⚙️ The apply Method:

Your apply method is designed to be highly flexible, accepting four parameters that control how filtering and searching are applied to your database query.

1. Builder $query
Type: Illuminate\Database\Eloquent\Builder

Requirement: Required (No default value).

Description: This is the starting point of your database query. It must be an existing Eloquent Query Builder instance. You pass this to FlexSearch, and the class modifies it by adding WHERE clauses for filtering and searching.

Example Usage: Post::query() or User::where('is_active', true).

2. array $filters
Type: array

Default: [] (An empty array).

Description: This parameter allows you to apply simple, direct equality filters. The array keys must match your database column names, and the values are the exact values to match.

Logic: It applies a basic WHERE column = value for each item in the array.

Example Usage: To filter posts by a specific category and status:
```
$filters = [
    'category_id' => 5,
    'status' => 'published'
];
```

3. ?string $searchTerm
Type: string or null

Default: null.

Description: This is the primary input for the keyword search. It's the text the user types into the search bar. The question mark (?) indicates that passing a null value is acceptable, in which case no keyword search is performed.

Example Usage: "Laravel package" or "best article".

4. array $searchableColumns
Type: array

Default: [] (An empty array).

Description: This list tells the FlexSearch method which specific database columns should be included in the keyword search. This array must be provided if a $searchTerm is present.

Logic: The package will apply LIKE '%term%' conditions across all columns listed here, making the search flexible.

Example Usage:

``` 
$searchableColumns = [
    'title', 
    'description', 
    'tags'
];
```

🔍 Keyword Search Logic Explained
The keyword search functionality is designed to be highly effective by combining terms with AND and columns with OR.

The provided $searchTerm is split by spaces into individual words (or "terms").

The query is constructed so that every single term in the search string must match an entry.

For each individual term, it applies a group of OR clauses across all $searchableColumns using the LIKE '%term%' operator.

Example Query Output (Conceptual)
If your search term is "red sport" and searchable columns are ['name', 'description'], the generated SQL WHERE clause will conceptually look like this:

```
WHERE (
    (name LIKE '%red%' OR description LIKE '%red%')
    AND (name LIKE '%sport%' OR description LIKE '%sport%')
)
```
