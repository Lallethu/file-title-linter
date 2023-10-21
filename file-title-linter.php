<?php

$folder = $argv[1] ?? null;
$image_size_pattern = '/@(\d+x\d+)\.([a-zA-Z]+)/';
$multiple_hyphen_pattern = '/-{2,}/';


function getFiles($folder)
{
  $files = scandir($folder);
  $files = array_diff($files, ['.', '..']);
  return $files;
}

if (!isset($folder))
  exit("Please provide a folder name\nTry: php file-title-linter.php <folder-name>\n");

if (!is_dir($folder))
  exit("Folder <$folder> is not a valid a folder or does not exist");

$files = getFiles($folder);

foreach ($files as $file) {
  if (is_dir($file)) continue;
  $base_file_name = $folder . DIRECTORY_SEPARATOR . $file;
  $new_file_name = str_replace(
    [" ", "_", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "é", "è", "ê", "ë", "à", "ç", "œ", "ù", "ô", "ö", "ä", "â", "'"],
    ["-", "-", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "e", "e", "e", "e", "a", "c", "oe", "u", "o", "o", "a", "a", "-"],
    $base_file_name
  );

  if (count($result_regex = preg_split($image_size_pattern, $new_file_name, -1, PREG_SPLIT_DELIM_CAPTURE)) > 1) {
    $new_file_name = $result_regex[0] . "." . $result_regex[2];
    if ($base_file_name === $new_file_name) {
      echo "File <$base_file_name> is already well formatted\n";
      continue;
    }
    if (preg_match($multiple_hyphen_pattern, $new_file_name)) {
      $new_file_name = preg_replace($multiple_hyphen_pattern, "-", $new_file_name);
    }
    if (rename($base_file_name, $new_file_name))
      echo "File <$base_file_name> renamed successfully to <$new_file_name>\n";
  } else {
    if (preg_match($multiple_hyphen_pattern, $new_file_name)) {
      $new_file_name = preg_replace($multiple_hyphen_pattern, "-", $new_file_name);
    }

    if ($base_file_name === $new_file_name) {
      echo "File <$base_file_name> is already well formatted\n";
      continue;
    }

    rename($base_file_name, $new_file_name);
    echo "File <$base_file_name> renamed successfully to <$new_file_name>\n";
  }
}
