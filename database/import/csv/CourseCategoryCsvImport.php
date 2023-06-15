<?php

class CourseCategoryCsvImport
{

    private array $results;
    private array $errors;

    private $subCategories = 'sub_categories';
    private $subCategoryName = 'category_name';
    private $category = 'category_name';
    private $categoryIndexes = [];
    private $subCategoryIndexes = [];

    private int $currentCategoryIndex;
    private int $currentSubCategoryIndex;


    private function findBySlugOrTitle($table, $title, $slug, ?array $extraWhere = []) {
        $where['query'] = '(slug = :slug OR title = :title)';
        if (isset($extraWhere['query'])) {
            $where['query'] .= " {$extraWhere['query']}";
        }
        $where['data'] = [
            'slug' => $slug,
            'title' => $title,
        ];
        if(isset($extraWhere['data'])) {
            $where['data'] = array_merge($where['data'], $extraWhere['data']);
        }
        return ORM::for_table($table)
            ->whereRaw($where['query'], $where['data'])
            ->findOne();
    }

    private function importCourses(bool $fromSubCat, array $data, ORM $category) {
        foreach ($data as $subCategoryCourse) {
            $courseName = trim($subCategoryCourse);
            $courseSlug = RepositoryHelpers::createSlug($courseName);

            $findCourse = $this->findBySlugOrTitle('courses', $courseName, $courseSlug);
            if (!$findCourse) {
                if ($fromSubCat) {
                    $this->addSubCategoryError('Error finding course', [
                        'course_name' => $courseName,
                    ]);
                } else {
                    $this->addCategoryError('Error finding course', [
                        'course_name' => $courseName,
                    ]);
                }
                continue;
            } else {
                $findCourseCategory = ORM::for_table('courseCategoryIDs')
                    ->where('courseID', $findCourse->get('id'))
                    ->findOne();
                if (!$findCourseCategory) {
                    $create = ORM::for_table('courseCategoryIDs')->create([
                        'course_id' => $findCourse->get('id'),
                        'category_id' => $category->get('id'),
                    ]);
                    if (!$create->save()) {
                        if ($fromSubCat) {
                            $this->addSubCategoryError('Error creating course category relation', [
                                'course_name' => $courseName,
                                'courseID' => $findCourse->get('id'),
                                'categoryID' => $category->get('id'),
                            ]);
                        } else {
                            $this->addCategoryError('Error creating course category relation', [
                                'course_name' => $courseName,
                                'courseID' => $findCourse->get('id'),
                                'categoryID' => $category->get('id'),
                            ]);
                        }
                        continue;
                    }
                } else {
                    $findCourseCategory->category_id = $category->get('id');
                    if (!$findCourseCategory->save()) {
                        if ($fromSubCat) {
                            $this->addSubCategoryError('Error updating course category relation', [
                                'course_name' => $courseName,
                                'courseID' => $findCourse->get('id'),
                                'categoryID' => $category->get('id'),
                            ]);
                        } else {
                            $this->addCategoryError('Error updating course category relation', [
                                'course_name' => $courseName,
                                'courseID' => $findCourse->get('id'),
                                'categoryID' => $category->get('id'),
                            ]);
                        }
                        continue;
                    }
                }
            }
        }
        return true;
    }
    private function importSubCategories(array $data, ORM $parentCategory) {
        foreach ($data as $index => $subCategory) {
            $this->currentSubCategoryIndex = $index;
            $subCategoryName = trim($subCategory[$this->subCategoryName]);
            $subCategorySlug = RepositoryHelpers::createSlug(trim($subCategoryName));

            $findSubCategoryQuery = $this->findBySlugOrTitle('courseCategories', $subCategoryName, $subCategorySlug);
            $subCatCreateData = [];
            if (!$findSubCategoryQuery) {
                $subCatCreateData['parentID'] = $parentCategory->get('id');
                $subCatCreateData['title'] = $subCategoryName;
                $subCatCreateData['slug'] = $subCategorySlug;
                $findSubCategoryQuery = ORM::for_table('courseCategories')->create($subCatCreateData);
                if (!$findSubCategoryQuery->save()) {
                    $this->addSubCategoryError('Error creating subcategory', [
                        'sub_category' => $subCategoryName
                    ]);
                    continue;
                }
            } else {
                $findSubCategoryQuery->parentID = $parentCategory->get('id');
                if (!$findSubCategoryQuery->save()) {
                    $this->addSubCategoryError('Error updating subcategory', [
                        'sub_category' => $subCategoryName
                    ]);
                    continue;
                }
            }
            $this->importCourses(true, $subCategory['data'], $findSubCategoryQuery);
        }
        return true;
    }
    private function importCategories() {
        foreach ($this->categoryIndexes as $index => $categoryData) {
            $this->currentCategoryIndex = $index;
            $categoryName = trim($categoryData[$this->category]);
            $categoryNameSlug = RepositoryHelpers::createSlug(trim($categoryName));
            $findCategoryQuery = $this->findBySlugOrTitle('courseCategories', $categoryName, $categoryNameSlug, [
                'query' => 'AND parentID IS NULL'
            ]);
            $createData = [];
            if (!$findCategoryQuery) {
                $createData['title'] = $categoryName;
                $createData['slug'] = $categoryNameSlug;
                $findCategoryQuery = ORM::for_table('courseCategories')->create($createData);
                if (!$findCategoryQuery->save()) {
                    $this->addCategoryError('Error saving category', [
                        $this->category => $categoryName
                    ]);
                    continue;
                }
            }
            if (is_array($categoryData[$this->subCategories])) {
                $this->importSubCategories($categoryData[$this->subCategories], $findCategoryQuery);
            }
            if (is_array($categoryData['data'])) {
                $this->importCourses(false, $categoryData['data'], $findCategoryQuery);
            }
        }
    }

    public function importFromCsvFile(array $fileData) {
        require_once(APP_ROOT_PATH . 'repositories/helpers/RepositoryHelpers.php');
        try {
            $file = fopen($fileData['tmp_name'], 'r');
            if (!$file) {
                return [
                    'success' => false,
                    'message' => 'Error file path is invalid'
                ];
            }
            $data = [];
            while (($line = fgetcsv($file)) !== FALSE) {
                $data[] = $line;
            }
            $this->buildCategoryImportData($data);
            $this->importCategories();
            unlink($fileData['tmp_name']);
            return [
                'success' => true
            ];
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    public function importFromGoogleSheets($url) {
        require_once(APP_ROOT_PATH . 'builders/google/sheets/GoogleSheets.php');
        require_once(APP_ROOT_PATH . 'repositories/helpers/RepositoryHelpers.php');
        $googleSheets = new GoogleSheets();
        $data = $googleSheets->fetchSpreadsheet($url, 'NSA');
        if (!$data['success']) {
            return $data;
        }
        $this->buildCategoryImportData($data['data']);
        $this->importCategories();
        return [
            'success' => true
        ];
    }

    public function buildCategoryImportData($data = [])
    {
        foreach ($data as $rowIndex => $row) {
            if (isset($this->limit) && $rowIndex > $this->limit) {
                return;
            }
            if (!$rowIndex) {
                $this->buildCategoryIndexes($row);
                continue;
            }

            foreach ($row as $index => $value) {
                $value = trim($value);
                if (!$index) {
                    $catIndexesFirstKey = array_key_first($this->categoryIndexes);
                    if (!is_array($this->categoryIndexes[$catIndexesFirstKey][$this->subCategories])) {
                        $this->categoryIndexes[$catIndexesFirstKey][$this->subCategories] = [];
                    }

                    if (!empty($value) && $value !== 'Subcategory') {
                        $this->addSubCategoryTrack($index, $value);
                    }
                    continue;
                }

                $categoryIndex = $this->findCategoryByIndex($index);
                if ($categoryIndex !== false && !is_null($categoryIndex)) {
                    if (empty($value)) {
                        continue;
                    } else {
                        $subCatIndex = array_search($index - 1, array_column($this->subCategoryIndexes, 'index'));
                        if ($subCatIndex !== false) {
                            $subCategory = $this->subCategoryIndexes[$subCatIndex]['name'];
                            $findSubCatIndex = $this->findCategorySubCategory($categoryIndex, $subCategory);
                            if ($findSubCatIndex === false) {
                                $subCatData = [
                                    $this->subCategoryName => $subCategory,
                                    'data' => [$value]
                                ];
                                $this->categoryIndexes[$categoryIndex][$this->subCategories][] = $subCatData;
                            } else {
                                $this->categoryIndexes[$categoryIndex][$this->subCategories][$findSubCatIndex]['data'][] = $value;
                            }
                        } else {
                            $this->categoryIndexes[$categoryIndex]['data'][] = $value;
                        }
                    }
                } else {
                    if (empty($value)) {
                        continue;
                    } else {
                        $this->addSubCategoryTrack($index, $value);
                    }
                }
            }
        }
    }


    private function addSubCategoryTrack($subCategoryIndex, $value)
    {
        $findSubCatTrackIndex = array_search($subCategoryIndex, array_column($this->subCategoryIndexes, 'index'));
        if ($findSubCatTrackIndex === false) {
            $this->subCategoryIndexes[] = [
                'name' => $value,
                'index' => $subCategoryIndex
            ];
        } else {
            $this->subCategoryIndexes[$findSubCatTrackIndex]['name'] = $value;
        }
    }

    private function findCategorySubCategory($catIndex, $subCategory)
    {
        if (!isset($this->categoryIndexes[$catIndex][$this->subCategories])) {
            return false;
        }
        return array_search(
            $subCategory,
            array_column($this->categoryIndexes[$catIndex][$this->subCategories], $this->subCategoryName)
        );
    }

    private function findCategoryByIndex($index)
    {
        return array_search(
            $index,
            array_column($this->categoryIndexes, 'index')
        );
    }

    private function buildCategoryIndexes($row)
    {
        foreach ($row as $index => $value) {
            if (!$index) {
                continue;
            }
            if (!empty($value)) {
                $this->categoryIndexes[] = [
                    'index' => $index,
                    $this->category => $value
                ];
            }
        }
    }

    /**
     * @param array $results
     */
    public function addResult(array $results): void
    {
        $this->results[] = $results;
    }

    /**
     * @param array $errors
     */
    public function addError(array $errors): void
    {
        $this->errors[] = $errors;
    }
    public function addCategoryError(string $message, array $error): void
    {
        if (!is_array($this->categoryIndexes[$this->currentCategoryIndex]['errors'])) {
            $this->categoryIndexes[$this->currentCategoryIndex]['errors'] = [];
        }
        $error['message'] = $message;
        $this->categoryIndexes[$this->currentCategoryIndex]['errors'][] = $error;
    }

    public function addSubCategoryError(string $message, array $error): void
    {
        if (!is_array($this->categoryIndexes[$this->currentCategoryIndex][$this->subCategories][$this->currentSubCategoryIndex]['errors'])) {
            $this->categoryIndexes[$this->currentCategoryIndex][$this->subCategories][$this->currentSubCategoryIndex]['errors'] = [];
        }
        $error['message'] = $message;
        $this->categoryIndexes[$this->currentCategoryIndex][$this->subCategories][$this->currentSubCategoryIndex]['errors'][] = $error;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        $errors = array_map(function ($category) {
            unset($category['data'], $category['index']);
            if (is_array($category[$this->subCategories])) {
                $category[$this->subCategories] = array_map(function ($subCats) {
                    unset($subCats['data']);
                    if (!is_array($subCats['errors'])) {
                        return false;
                    }
                    return $subCats;
                }, $category[$this->subCategories]);

                $category[$this->subCategories] = array_filter($category[$this->subCategories], function ($subCats) {
                    return $subCats;
                }, ARRAY_FILTER_USE_BOTH);
                $category[$this->subCategories] = array_values($category[$this->subCategories]);
            }
            if (!is_array($category['errors']) && !$category[$this->subCategories]) {
                return false;
            }
            return $category;
        }, $this->categoryIndexes);

        $filterErrors = array_filter($errors, function ($category) {
            return  $category;
        }, ARRAY_FILTER_USE_BOTH);
        return array_values($filterErrors);
    }

    /**
     * @return array
     */
    public function getCategoryIndexes(): array
    {
        return $this->categoryIndexes;
    }

}