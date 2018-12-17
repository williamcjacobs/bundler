<?php

/** BEGIN CONFIG **/


$DEV_MODE = false; //If set to true files won't be cached. (Useful for Development)

$TMP_FOLDER = 'tmp/'; //The folder, relative to this file, that will store the cached versions of bundled files.

$BUNDLE_HASH_ALGO = 'md5'; //The hash algorithm used to create file names.


/** END OF CONFIG **/

if($DEV_MODE){

	// We clear the cache every time in development mode.
	empty_bundle_cache();
}

function empty_bundle_cache(){
	global $TMP_FOLDER;

	//Delete all versions of all files in the tmp folder/
	array_map('unlink', glob($TMP_FOLDER.'/*.*'));
}

function delete_cached_bundle($filepathArray, $version='', $extension=''){

	//Use this function to clear the cached file of a specified version of a bundle.
	//Normally, specifying a new bundle version or clearing the entire cache would be a simpler alternative.
	global $TMP_FOLDER, $BUNDLE_HASH_ALGO;

	$path = bundle_path($filepathArray, $version, $extension);

	if ( file_exists($path) ) {

		// Delete the file if it exists.
		return unlink($path);
	}

	return false;
}

function bundle_files($filepathArray, $version='', $extension=''){

	//Use this function to bundle together js and css files in the header.
	//In development mode, files will not be cached. (so the $version parameter is only useful when not in development mode)
	//If no extension is provided the extension from the first file will be used.
	global $DEV_MODE, $TMP_FOLDER, $BUNDLE_HASH_ALGO;

	// If we're in development mode, we append the time to the version so that the browser won't cache the bundled file.
	$version = (empty($version)? '':$version.($DEV_MODE?'d'.time():''));
	$contents = '';

	if ( empty($filepathArray) ) {


		//No files, No need to continue.
		return;
	}

	$path = bundle_path($filepathArray, $version, $extension);

	//If this version of the bundled files already exists, 
	//it's path is returned with no need for pulling file contents.
	if ( file_exists($path) ) {
	
		return $path;
	}

	foreach ($filepathArray as $filePath) {
	
		// Move to a new line and append the contents of the next file in the bundle.	
			
		$contents .= "\n".file_get_contents($filePath);
	}

	file_put_contents($path, $contents);

	return $path;
}

function bundle_path($filepathArray, $version='', $extension=''){

	// This function returns a unique file path for each file bundle.
	// Note that rearranging filepaths in the $filepathArray will cause the bundle to be stored as a different file.
	// e.g. ['myscript1.js', 'myscript2.js'] is a different bundle than ['myscript2.js', 'myscript1.js']
	global $BUNDLE_HASH_ALGO, $TMP_FOLDER;

	if(empty($filepathArray)){

		// No files, something must be wrong.
		return;
	}

	$paths = '';

	foreach ($filepathArray as $filePath) {
		
		// Combine all of the files paths to use in a hash that will become our new file name.
		// So the same set of files will always produce the same output if the version is set the same. 
		$paths .= $filePath;
	}

	if(empty($extension)){
		
		// Grab the extension of the first file if no $extension was specificed.
		// Supports ".." parent directory selectors but not single "." current directory selectors.
		// Also works for double extension files like ".min.js"

		$ex = explode('.', str_replace('..', '', reset($filepathArray)));
		$extension = '';

		for ($i = 1; $i<sizeof($ex); $i++) {
	
			$extension .= '.'.$ex[$i];
		}
	
	}

	// Hash the combined file paths and version using the specified algorithm ( $BUNDLE_HASH_ALGO )
	// Each version generates its own file.
	$file_name = hash($BUNDLE_HASH_ALGO, $paths.$version);

	//Create the temporary folder if it doesn't exist.
	if(!file_exists($TMP_FOLDER)){

		mkdir($TMP_FOLDER);
	}

	$path = $TMP_FOLDER.$file_name.$extension;

	return $path;
}