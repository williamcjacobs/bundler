# Inline Bundler 

This is a PHP file bundler that can be used in the `<head>` section of html to combine JS and CSS files dynamically.

# Usage:

    <link rel="stylesheet" href="main_styles.css" >
    <link rel="stylesheet" href="some_other_styles.css" >

To send the above files as a single file to the client (web browser),
you can combine the two css files in a php file like so:
  
    <?php include('pathtobundler/bundler.php') ?>
  
    <link rel="stylesheet" href="<?= bundle_files(['main_styles.min.css', 'some_other_styles.min.css'], '1.0') ?>" >

Which will produce something like the following in a user's browser:
     
    <link rel="stylesheet" href="tmp/d9c860f7c833879356983d2f2a6f30d6.min.css">
    
The `bundle_files` function takes the following parameters:

`bundle_files($filepathArray, $version='', $extension='')`

The `$version` and `$extension` parameters are optional.

If no extension is specified, the extension of the first file is used. 
Multiple file extensions, such as `.min.js` are preserved.

# Details:

The bundled file is generated once, and is cached for use in subsequent calls.
The bundled files's name is dependant on the order of the files to be bundled though, so 

`bundle_files(['main_styles.min.css', 'some_other_styles.min.css'], '1.0')`

will produce a different file path than 

`bundle_files(['some_other_styles.min.css', 'main_styles.min.css'], '1.0')` 

Also note that the version is hashed into the file name.

# Config:

Caching can be disabled by setting `$DEV_MODE = true;` 
(This will clear the cache on each include of the bundler.php file)

Change the `$TMP_FOLDER` variable to change the path to the cache folder.

The bundler `$BUNDLE_HASH_ALGO` is a string representing the hash function to be used. By default it is set to `'md5'`. 
For a list of available hash algorithms, you can look [here](http://php.net/manual/en/function.hash-algos.php).

# Functions: 

`function bundle_files($filepathArray, $version='', $extension='')` - Returns the path (relative to the current file) to the generated bundled file.

`function empty_bundle_cache()` - Clears the entire cache.

`function delete_cached_bundle($scriptList, $version='', $extension='')` - Deletes the cached version of a specific bundled file.

`function bundle_path($filepathArray, $version='', $extension='')` - Used internally to get the path of a specific file bundle. Returns a string representing a generated path for the specified bundle and version without creating a bundle. 
