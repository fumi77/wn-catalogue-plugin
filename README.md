# Catalogue Plugin

A simple, extensible catalogueging platform for Winter CMS.

[Catalogue & Forum Building Tutorial Video](https://player.vimeo.com/video/97088926)

## Installation

This plugin is available for installation via [Composer](http://getcomposer.org/).

```bash
composer require winter/wn-catalogue-plugin
```

## Editing posts

The plugin uses the markdown markup for the posts. You can use any Markdown syntax and some special tags for embedding images and videos (requires Winter Catalogue Video plugin). To embed an image use the image placeholder:

    ![1](image)

The number in the first part is the placeholder index. If you use multiple images in a post you should use an unique index for each image:

    ![1](image)

    ![2](image)

You can also add classes or ids to images by using the [markdown extra](http://michelf.ca/projects/php-markdown/extra/) syntax:

    ![1](image){#id .class}

## Excerpt Vs. Read more

Posts are managed by selecting *Catalogue > Posts* from the menu. Each post can contain an excerpt by entering some text in this field on the *Manage* tab. This content is displayed on the page using the `summary` attribute of the catalogue post.

    {{ post.summary|raw }}

Alternatively this field can be left blank and the excerpt can be captured from the main content (*Edit* tab). Use the special tag `<!-- more -->` to specify a summary from the main content, all content above this tag will be treated as the summary. For example:

    This is a great introduction to a great catalogue post. This text is included as part of the excerpt / summary.

    <!-- more -->

    Let's dive in to more detail about why this post is so great. This text will not be included in the summary.

Finally, if no excerpt is specified and the "more" tag is not used, the catalogue post will capture the first 600 characters of the content and use this for the summary.

## Implementing front-end pages

The plugin provides several components for building the post list page (archive), category page, post details page and category list for the sidebar.

### Post list page

Use the `cataloguePosts` component to display a list of latest catalogue posts on a page. The component has the following properties:

* **pageNumber** - this value is used to determine what page the user is on, it should be a routing parameter for the default markup. The default value is **{{ :page }}** to obtain the value from the route parameter `:page`.
* **categoryFilter** - a category slug to filter the posts by. If left blank, all posts are displayed.
* **postsPerPage** - how many posts to display on a single page (the pagination is supported automatically). The default value is 10.
* **noPostsMessage** - message to display in the empty post list.
* **sortOrder** - the column name and direction used for the sort order of the posts. The default value is **published_at desc**.
* **categoryPage** - path to the category page. The default value is **catalogue/category** - it matches the pages/catalogue/category.htm file in the theme directory. This property is used in the default component partial for creating links to the catalogue categories.
* **postPage** - path to the post details page. The default value is **catalogue/post** - it matches the pages/catalogue/post.htm file in the theme directory. This property is used in the default component partial for creating links to the catalogue posts.
* **exceptPost** - ignore a single post by its slug or unique ID. The ignored post will not be included in the list, useful for showing other/related posts.
* **exceptCategories** - ignore posts from a comma-separated list of categories, given by their unique slug. The ignored posts will not be included in the list.

The cataloguePosts component injects the following variables to the page where it's used:

* **posts** - a list of catalogue posts loaded from the database.
* **postPage** - contains the value of the `postPage` component's property.
* **category** - the catalogue category object loaded from the database. If the category is not found, the variable value is **null**.
* **categoryPage** - contains the value of the `categoryPage` component's property.
* **noPostsMessage** - contains the value of the `noPostsMessage` component's property.

The component supports pagination and reads the current page index from the `:page` URL parameter. The next example shows the basic component usage on the catalogue home page:

    title = "Catalogue"
    url = "/catalogue/:page?"

    [cataloguePosts]
    postsPerPage = "5"
    ==
    {% component 'cataloguePosts' %}

The next example shows the basic component usage with the category filter:

    title = "Catalogue Category"
    url = "/catalogue/category/:slug/:page?"

    [cataloguePosts]
    categoryFilter = "{{ :slug }}"
    ==
    function onEnd()
    {
        // Optional - set the page title to the category name
        if ($this->category)
            $this->page->title = $this->category->name;
    }
    ==
    {% if not category %}
        <h2>Category not found</h2>
    {% else %}
        <h2>{{ category.name }}</h2>

        {% component 'cataloguePosts' %}
    {% endif %}

The post list and the pagination are coded in the default component partial `plugins/winter/catalogue/components/posts/default.htm`. If the default markup is not suitable for your website, feel free to copy it from the default partial and replace the `{% component %}` call in the example above with the partial contents.

### Post page

Use the `cataloguePost` component to display a catalogue post on a page. The component has the following properties:

* **slug** - the value used for looking up the post by its slug. The default value is **{{ :slug }}** to obtain the value from the route parameter `:slug`.
* **categoryPage** - path to the category page. The default value is **catalogue/category** - it matches the pages/catalogue/category.htm file in the theme directory. This property is used in the default component partial for creating links to the catalogue categories.

The component injects the following variables to the page where it's used:

* **post** - the catalogue post object loaded from the database. If the post is not found, the variable value is **null**.

The next example shows the basic component usage on the catalogue page:

    title = "Catalogue Post"
    url = "/catalogue/post/:slug"

    [cataloguePost]
    ==
    <?php
    function onEnd()
    {
        // Optional - set the page title to the post title
        if ($this->post)
            $this->page->title = $this->post->title;
    }
    ?>
    ==
    {% if post %}
        <h2>{{ post.title }}</h2>

        {% component 'cataloguePost' %}
    {% else %}
        <h2>Post not found</h2>
    {% endif %}

The post details is coded in the default component partial `plugins/winter/catalogue/components/post/default.htm`.

### Category list

Use the `catalogueCategories` component to display a list of catalogue post categories with links. The component has the following properties:

* **slug** - the value used for looking up the current category by its slug. The default value is **{{ :slug }}** to obtain the value from the route parameter `:slug`.
* **displayEmpty** - determines if empty categories should be displayed. The default value is false.
* **categoryPage** - path to the category page. The default value is **catalogue/category** - it matches the pages/catalogue/category.htm file in the theme directory. This property is used in the default component partial for creating links to the catalogue categories.

The component injects the following variables to the page where it's used:

* **categoryPage** - contains the value of the `categoryPage` component's property. 
* **categories** - a list of catalogue categories loaded from the database.
* **currentCategorySlug** - slug of the current category. This property is used for marking the current category in the category list.

The component can be used on any page. The next example shows the basic component usage on the catalogue home page:

    title = "Catalogue"
    url = "/catalogue/:page?"

    [catalogueCategories]
    ==
    ...
    <div class="sidebar">
        {% component 'catalogueCategories' %}
    </div>
    ...

The category list is coded in the default component partial `plugins/winter/catalogue/components/categories/default.htm`.

### RSS feed

Use the `catalogueRssFeed` component to display an RSS feed containing the latest catalogue posts. The following properties are supported:

* **categoryFilter** - a category slug to filter the posts by. If left blank, all posts are displayed.
* **postsPerPage** - how many posts to display on the feed. The default value is 10.
* **cataloguePage** - path to the main catalogue page. The default value is **catalogue** - it matches the pages/catalogue.htm file in the theme directory. This property is used in the RSS feed for creating links to the main catalogue page.
* **postPage** - path to the post details page. The default value is **catalogue/post** - it matches the pages/catalogue/post.htm file in the theme directory. This property is used in the RSS feed for creating links to the catalogue posts.

The component can be used on any page, it will hijack the entire page cycle to display the feed in RSS format. The next example shows how to use it:

    title = "RSS Feed"
    url = "/catalogue/rss.xml"

    [catalogueRssFeed]
    cataloguePage = "catalogue"
    postPage = "catalogue/post"
    ==
    <!-- This markup will never be displayed -->

## Markdown guide

Winter CMS supports [standard markdown syntax](http://daringfireball.net/projects/markdown/) as well as [extended markdown syntax](http://michelf.ca/projects/php-markdown/extra/)

### Classes and IDs

Classes and IDs can be added to images and other elements as shown below:

```
[link](url){#id .class}
![1](image){#id .class}
# Winter CMS  {#id .class}
```

### Fenced code catalogues

Markdown extra makes it possible to use fenced code blocks. With fenced code blocks you do not need indentation on the areas you want to mark as code:


    ```
    Code goes here
    ```
    
You can also use the `~` symbol:

    ~~~
    Code goes here
    ~~~

### Tables

A *simple* table can be defined as follows:

```
First Header  | Second Header
------------- | -------------
Content Cell  | Content Cell 
Content Cell  | Content Cell 
```

If you want to you can also add a leading and tailing pipe:

```
| First Header  | Second Header |
| ------------- | ------------- |
| Content Cell  | Content Cell  |
| Content Cell  | Content Cell  |
```

To add alignment to the cells you simply need to add a `:` either at the start or end of a separator:

```
| First Header  | Second Header |
| :------------ | ------------: |
| Content Cell  | Content Cell  |
| Content Cell  | Content Cell  |
```

To center align cell just add `:` on both sides:

```
| First Header  | Second Header |
| ------------- | :-----------: |
| Content Cell  | Content Cell  |
| Content Cell  | Content Cell  |
```

### Definition lists

Below is an example of a simple definition list:

```
Laravel
:   A popular PHP framework

Winter CMS
:   Awesome CMS built on Laravel
```

A term can also have multiple definitions:

```
Laravel
:   A popular PHP framework

Winter CMS
:   Awesome CMS built on Laravel
:   Supports markdown extra
```

You can also associate more than 1 term to a definition:

```
Laravel
Winter CMS
:   Built using PHP
```

### Footnotes

With markdown extra it is possible to create reference style footnotes:

```
This is some text with a footnote.[^1]

[^1]: And this is the footnote.
```

### Abbreviations

With markdown extra you can add abbreviations to your markup. The use this functionality first create a definition list:

```
*[HTML]: Hyper Text Markup Language
*[PHP]:  Hypertext Preprocessor
```

Now markdown extra will convert all occurrences of `HTML` and `PHP` as follows:

```
<abbr title="Hyper Text Markup Language">HTML</abbr>
<abbr title="Hypertext Preprocessor">PHP</abbr>
```
