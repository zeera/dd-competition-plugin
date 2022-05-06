<?php

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Core;

/**
 * Model Core
 */
class Model extends WpDatabase
{
    public $pointer_id = null;

    public $last_insert_id = null;

    public array $options = [];

    public array $data = [];

    public array $errors = [];

    public int $data_count = 0;

    public int $recordsperpage = 15;

    public array $order_dirs = ['ASC' => 'ASC', 'DESC' => 'DESC'];

    public string $default_order_by = 'id';

    public string $default_order_dir = 'DESC';

    public string $table_name = '';

    public array $selected = [];

    public bool $load_database = true;

    public string $upload_dir = '';

    public string $logfilelocation = '';

    /*
     * This calls the passed database_name from the config.php file as defined by
     * DATABASE_<database_name> in that file;
     */

    // Loads the initial data can be overloaded
    public function __construct()
    {
        $this->loadOptions();
        $this->construct();
        if ($this->cache_path == '')
            $this->cache_path = dirname(__DIR__, 2) . '/cache/'; //Two directories up
    }

    /** To be overridden for option loading */
    public function loadOptions()
    {
    }

    /** To be overridden for construction */
    public function construct()
    {
    }

    /** Check if a password matches a hash, or if hash == null, return the hashed password */
    public function hasher(string $password, string $hash = null): mixed
    {
        // if encrypted data is passed, check it against input ($info)
        if ($hash === null) {
            return password_hash($password, PASSWORD_BCRYPT);
        }

        return password_verify($password, $hash);
    }

    /** Get selected data */
    public function get(string $name): mixed
    {
        return $this->selected[$name] ?? null;
    }

    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    /** Get an error */
    public function getError($name)
    {
        return isset($this->errors[$name]) ?? false;
    }

    /** Selects a result from data directly */
    public function select($data_name, $pointer_id)
    {
        $test = $this->data[$data_name][$pointer_id] ?? false;

        if ($test === false) {
            return false;
        }

        $this->pointer_id[$data_name] = $pointer_id;
        $this->selected = $this->data[$data_name][$pointer_id];
        return true;
    }

    /**
     * Load the next item
     */
    public function next($data_name)
    {
        if (!isset($this->pointer_id[$data_name])) {
            return $this->select($data_name, 0);
        }

        $tmp = (int) $this->pointer_id[$data_name] + 1;
        $result = $this->select($data_name, $tmp);

        if ($result) {
            $this->pointer_id[$data_name] = $tmp;
        }

        return $result;
    }

    /**
     * Load the previous item
     */
    public function previous($data_name)
    {
        if (!isset($this->pointer_id[$data_name])) {
            return $this->select($data_name, 0);
        }

        $tmp = (int) $this->pointer_id[$data_name] - 1;
        $result = $this->select($data_name, $tmp);

        if ($result) {
            $this->pointer_id[$data_name] = $tmp;
        }

        return $result;
    }

    /**
     * Add pagination to a query
     */
    public function sqlPagination(string $query, int $currentPage, int $recordsPerPage): string
    {
        $offset = 0;
        if ($currentPage > 0) {
            $offset = $currentPage * $recordsPerPage;
        }

        $query =
            $query .
            " OFFSET $offset ROWS FETCH NEXT $recordsPerPage ROWS ONLY ";

        return $query;
    }

    /**
     * Add an option to the model
     */
    public function addOption(string $name, mixed $array)
    {
        $this->options[$name] = $array;
    }

    /**
     * Add a data result to the model
     */
    public function addDataResult(string $name, mixed $array)
    {
        $this->data[$name] = $array;
    }

    /**
     * Get data from the model
     */
    public function getData(mixed $data_name, mixed $field, int $increment = 0): mixed
    {
        $returnvalue = '';

        if (isset($this->data[$data_name][$increment])) {
            if (isset($this->data[$data_name][$increment][$field])) {
                $returnvalue = $this->data[$data_name][$increment][$field];
            }
        }

        return $returnvalue;
    }

    /**
     * Checks to see object exists in the data result ($this->data)
     * returns appropriate array if found otherwise returns blank array
     *
     * @param array $name
     *            - what the name of the data result
     */
    public function getDataSource($name)
    {
        if (!isset($this->data[$name])) {
            return [];
        }

        return $this->data[$name];
    }

    /**
     * Get an option from the model
     */
    public function getOption($name)
    {
        if (
            !isset($this->options[$name]) || (is_array($this->options[$name]) && count($this->options[$name]) == 0)
        ) {
            return [];
        }

        return $this->options[$name];
    }

    /**
     * Get a data array  from the model
     */
    public function getDataArray($database, $field)
    {
        $returnarray = [];

        if (!isset($this->data[$database])) {
            return $returnarray;
        }

        foreach ($this->data[$database] as $data) {
            $returnarray[] = $data[$field];
        }

        return $returnarray;
    }

    // data,folder,additionalprepend,tablename,
    // The trouble is that the upload file records what happened in a unknown way,
    // Different database designs might have the final upload/remove information handled differently
    //
    /**
     * Upload a file
     */
    public function uploadFile(array $fileData, string $directoryName, ?string $prependId = null, ?string $postpendId = null, ?string $subfolder = '')
    {
        $uploadDir = $this->uploadDir;
        $returndata = [];
        $directoryName = str_replace(['/', '\\'], '', $directoryName);

        // Checking for prepend id data, (int only)
        if ($prependId != false and strlen($prependId) > 0) {
            $prependId = (int) $prependId;
            $uploadDir .= $prependId . '/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
        }

        $uploadDir .= $directoryName; // Create the folder if it doesnt already exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Checking for postpend id data, (int only)
        if ($postpendId != false and strlen(trim($postpendId)) > 0) {
            $postpendId = (int) $postpendId;
            $uploadDir .= '/' . $postpendId;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
        }

        // If there is a subfolder
        if (isset($subfolder) and strlen($subfolder) > 0) {
            $folderName = str_replace(['/', '\\'], '', $subfolder);
            $uploadDir .= '/' . $folderName;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
        }

        if ($fileData != null) {
            // What files should never be uploaded
            $notallowedExts = ['exe', 'bat'];

            $temp = explode('.', $fileData['name']);
            $extension = end($temp);

            // Checking uploaded file extension
            if (in_array($extension, $notallowedExts)) {
                $this->errors['attachfile'] = 'Invalid File Extension';
            }
            if (count($this->errors) == 0) {
                // If the folder for the id does not exist, create it.
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = str_replace(
                    ['/', '\\'],
                    '',
                    basename($fileData['name'])
                );

                if (file_exists($uploadDir . '/' . $fileName)) {
                    $returndata['file_exists'] = true;

                    // $fileName = date ( 'YmdHis' ) . "_" . $fileName;
                }
                // The disired end location and name
                $uploadfile = $uploadDir . '/' . $fileName;

                // If there is an exiting file of that name, then rename the file to something unique?
                if (move_uploaded_file($fileData['tmp_name'], $uploadfile)) {
                    $this->finalinsertsuccess = true;
                } else {
                    $this->errors['file'] =
                        'Error Uploading File, Check filesize < 30m';
                }

                $returndata['file'] = [
                    'file_name' => $fileName,
                    'folder_name' => $directoryName,
                    'file_size' => $fileData['size'],
                    'subfolder' => $subfolder,
                ];
            }
        }
        return $returndata;
    }

    /**
     * Retreive a file
     */
    public function retrieveFile($fileName, $folderName, $prependId = null, $postpendId = null, $subfolder = '')
    {
        $uploadDir = $this->uploadDir;

        // Checking for prepend id data, (int only)
        if ($prependId != false and strlen($prependId) > 2) {
            $prependId = (int) $prependId;
            $uploadDir .= $prependId . '/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
        }

        $uploadDir .= $folderName;

        // Checking for postpend id data, (int only)
        if ($postpendId != false and strlen($postpendId) > 0) {
            $postpendId = (int) $postpendId;
            $uploadDir .= '/' . $postpendId;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
        }

        // Checking for postpend id data, (int only)
        if ($subfolder != '' and strlen($subfolder) > 0) {
            $subfolder = $subfolder;
            $uploadDir .= '/' . $subfolder;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
        }
        $path = $uploadDir . '/' . $fileName;
        $parts = explode('/', pathinfo($path, PATHINFO_DIRNAME));
        if ($postpendId !== false) {
            if (end($parts) !== (string) $postpendId) {
                if ($parts[count($parts) - 2] == (string) $postpendId) {
                } else {
                    print 'LFI Attempt';
                    exit();
                }
            }
        } else if (end($parts) !== (string) $folderName) {
            print 'LFI Attempt';
            exit();
        }

        if (!is_file($path)) {
            // file does not exist
            print 'File does not Exist';
        } else {
            // adding the mime checker/returner
            $mime = new Mime();

            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mime->mime_type($path));
            header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));

            readfile($path);
        }
    }

    /**
     * Create a short UUID
     */
    public function createShortUID($id)
    {
        $uuid = Conversion::convBase(
            $id,
            '0123456789',
            '0123456789ABCDEFGHIJKLMNPQRSTUVWXY'
        );
        $uuid =
            $uuid .
            'Z' .
            Conversion::convBase(
                rand(0, 100),
                '0123456789',
                '0123456789ABCDEFGHIJKLMNPQRSTUVWXY'
            );

        return $uuid;
    }

    /**
     * Returns a string of strlen()==$length
     * of url-safe charachters.
     */
    public function safe_random_string($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_.~';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randstring;
    }
}
