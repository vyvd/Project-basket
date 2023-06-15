<?php

require_once(__DIR__ . '/mediaController.php');
require_once(__DIR__ . '/viralQuestionController.php');
require_once(__DIR__ . '/viralAnswerController.php');

class viralQuizController extends Controller
{
    /**
     * @var mediaController
     */
    protected $medias;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $questionTable;

    /**
     * @var string
     */
    protected $answerTable;

    public function __construct()
    {
        $this->table = 'viralQuizzes';
        $this->questionTable = 'viralQuestions';
        $this->answerTable = 'viralAnswers';

        $this->medias = new mediaController();
    }

    public function saveQuiz(array $input)
    {

        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table($this->table)->find_one($input['id']);
            if (!$item->id) {
                $item = ORM::for_table($this->table)->create();
            }
        } else {
            $item = ORM::for_table($this->table)->create();
        }

        $data = $input;
        if (@$data['questions']) {
            $questions = $data['questions'];
            unset($data['questions']);
        }

        $item->set($data);

        $item->save();

        // Check if Questions
        if (@$questions) {
            foreach ($questions as $question) {
                $question['quizID'] = isset($question->quizID)
                    ? $question->quizID : $item->id;
                $this->saveQuizQuestion($question);
            }
        }

        return $item;

    }

    public function saveQuizQuestion(array $input)
    {

        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table($this->questionTable)
                ->find_one($input['id']);
        }

        if (!@$item->id) {
            $item = ORM::for_table($this->questionTable)->create();
        }

        $data = [
            'title'       => $input['title'],
            'quizID'      => $input['quizID'],
            'position'    => $input['position'],
            'description' => $input['description'],
        ];


        $item->set($data);

        $item->save();

        // Check if Image URL
        if (@$input['imageUrl']) {
            $model = [
                'type' => viralQuestionController::class,
                'id' => $item->id
            ];

            $this->medias->saveWPImage(array('full' => $input['imageUrl']), $model, 'picture', true);
        }

        // Check if Answers
        if (@$input['answers']) {
            foreach ($input['answers'] as $answer) {
                $answer['questionID'] = isset($answer->questionID)
                    ? $answer->questionID : $item->id;
                $this->saveQuizAnswer($answer);
            }
        }

        return $item;

    }

    public function saveQuizAnswer(array $input)
    {

        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table($this->answerTable)->find_one($input['id']);
        }

        if (!@$item->id) {
            $item = ORM::for_table($this->answerTable)->create();
        }

        $data = [
            'title'       => $input['title'],
            'questionID'  => $input['questionID'],
            'isCorrect'   => $input['isCorrect'],
            'description' => $input['description'],
        ];

        $item->set($data);

        $item->save();

        // Check if Image URL
        if (@$input['imageUrl']) {
            $model = [
                'type' => viralAnswerController::class,
                'id' => $item->id
            ];
            $this->medias->saveWPImage(array('full' => $input['imageUrl']), $model, 'picture', true);
        }

        return $item;

    }

}