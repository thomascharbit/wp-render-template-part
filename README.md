# render_template_part()

An alternative to the native WordPress function `get_template_part()` that can pass arguments to the local scope.

## Installation

- Using composer : `composer require freshflesh/wp-render-template-part`
- Or manually download and include the `src/wp-render-template-part.php` file

##Parameters

* **$slug**: (string) (Required) The slug name for the generic template.
* **$name**:  (string) (Optional) The name of the specialised template. Default value: null
* **$args**: (array)  (Optional) An associative array containing arguments to pass to the template. Default empty array.
* **$echo**: (boolean) (Optional) Wether to echo or return the rendered template. Default is true.

## Usage

To pass arguments to a template partial, use the `render_template_part()` function.

#### Simple example

**Parent template**

    <?php
        $args =  array(
                    'title'   => 'Hello there.',
                    'content' => 'Lorem ipsum sid amet'
                 );
                 
        render_template_part( 'block', 'widget', $args );


**Child template**

    <?php
        <div class="widget">
            <h2><?php echo $title; // will output 'Hello there.' ?></h2>
            <p><?php echo $content;  // will output 'Lorem ipsum sid amet' ?></p>
        </div>


#### Returning value example

  Useful when returning templates through AJAX for instance:

    <?php
        // get rendered template
        $html = render_template_part( 'partials/sidebar', 'article', array(
                     'post_id' => $post->ID
                ) );
        // send in json response 
        wp_send_json_success( array( $html );
  

## Features

#### WP_Query autoloading
Passing a WP_Query instance as a `query` argument will automatically set it as the current loop.


#### WP_Post autoloading
Passing a WP_Post instance as a `post_object` argument will automatically call `setup_post_data()` so you can start using `the_title()`, `the_content()`.

> Note: `query` and `post_object` are therefore reserved argument names

#### Extendable

2 hooks are provided to extend its behavior:

**Action:**
`do_action( "render_template_part_{$slug}", $slug, $name, $args, $echo );`

**Filter:**
`$args = apply_filters( "render_template_part_{$slug}_args", $args, $slug, $name, $echo );`

