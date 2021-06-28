<?php

require_once('models/StudentModel.php');
require_once('models/AssignmentModel.php');
require_once('models/TaskModel.php');
require_once('settings/roles.php');
require_once('helpers/RedirectHelper.php');

class StudentController
{
    public function viewCourses()
    {
        RedirectHelper::redirect(ROLE_STUDENT);

        $pageTitle = 'Courses';

        require('views/common/header.php');

        $studentId = $_SESSION['userId'];

        $studentModel = new StudentModel();
        $courses = $studentModel->getStudentCourses($studentId);

        $items =array();
        foreach ($courses as $course) {
            $grades = $studentModel->getCourseGrade($course['id']);
            $courseGrade = array();
            foreach ($grades as $grade) {
                $courseGrade[] = ($grade['assignment_percentage'] / 100) * (($grade['task_percentage'] / 100) * $grade['final_grade']);
            }
            $items[] = array(
                'id' => $course['id'],
                'title' => $course['title'],
                'description' => $course['description'],
                'year' => $course['year'],
                'semester' => $course['semester'],
                'grade' => array_sum($courseGrade),
            );
        }

        $_SESSION['courses'] = $items;
        require('views/student/student.php');
        require('views/common/footer.php');
    }

    public function viewAssignments($courseId)
    {
        RedirectHelper::redirect(ROLE_STUDENT);

        $pageTitle = 'Assignments';

        require('views/common/header.php');

        $courseModel = new CourseModel();
        $_SESSION['courseTitle'] = $courseModel->getCourseTitle($courseId);
        $studentModel = new StudentModel();
        $assignments  = $studentModel->getAssignments($courseId);
        $_SESSION['courseId'] = $courseId;

        $items = array();
        foreach ($assignments as $assignment) {
            $grades = $studentModel->getAssignmentGrade($assignment['id'], $_SESSION['userId']);
            $assignmentGrade = array();
            foreach ($grades as $grade) {
                $assignmentGrade[] = ($grade['grade_percentage'] / $grade['max_task_grade']) * $grade['final_grade'];
            }

            $items[] = array(
                'id' => $assignment['id'],
                'title' => $assignment['title'],
                'description' => $assignment['description'],
                'due_date' => $assignment['due_date'],
                'grade' => array_sum($assignmentGrade),
                'release_grade' => $assignment['release_grade']
            );
        }

        if (!is_null($items)) {
            $_SESSION['assignments'] = $items;
        }

        require('views/student/viewAssignments.php');
        require('views/common/footer.php');
    }

    public function viewAssignmentReport($assignmentId)
    {
        RedirectHelper::redirect(ROLE_STUDENT);

        $pageTitle = 'Assignment Report';

        require('views/common/header.php');

        $studentModel = new StudentModel();
        $assignmentModel = new AssignmentModel();
        $professorModel = new ProfessorModel();

        $navigationDetails = $professorModel->getNavigationDetailsAssignmentReport($assignmentId);
        $_SESSION['courseId'] = $courseId = $navigationDetails[0];
        $_SESSION['courseTitle'] = $courseTitle = $navigationDetails[1];
        $_SESSION['assignmentId'] = $assignmentId = $navigationDetails[2];
        $_SESSION['assignmentTitle'] = $assignmentTitle = $navigationDetails[3];

        $reports  = $studentModel->getTaskOutput($assignmentId, $_SESSION['userId']);
        $assignmentGradesForStudents = $studentModel->getAssignmentGradesForStudents($assignmentId);
        $assignmentName = $assignmentModel->getAssignmentTitle($assignmentId);
        $assignmentSubmissionNo = $studentModel->getAssignmentSubmissionNo($_SESSION['assignmentId'], $_SESSION['userId']);

        $gradesIntervalCount = array_fill(0, 11, 0);
        foreach ($assignmentGradesForStudents as $assignmentGradesForStudent) {
            $index = $assignmentGradesForStudent['grade'] / 10 % 10;

            if ($assignmentGradesForStudent['grade'] == 100) {
                $index = 10;
            }
            $gradesIntervalCount[$index]++;
        }

        $_SESSION['gradesIntervalCount'] = join(',', $gradesIntervalCount);
        $_SESSION['reports'] = $reports;
        $_SESSION['assignmentName'] = $assignmentName;
        $_SESSION['assignmentSubmissionNo'] = $assignmentSubmissionNo;

        require('views/student/viewAssignmentReport.php');
        require('views/common/footer.php');
    }

    public function viewTasks($assignmentId)
    {
        RedirectHelper::redirect(ROLE_STUDENT);

        $pageTitle = 'Tasks';

        require('views/common/header.php');

        $userId = $_SESSION['userId'];

        $assignmentModel = new AssignmentModel();
        $_SESSION['assignmentTitle'] = $assignmentModel->getAssignmentTitle($assignmentId);

        $taskModel = new TaskModel();
        $_SESSION['tasks'] = $taskModel->getTasks($assignmentId);
        $tasks = $_SESSION['tasks'];


        $studentModel = new StudentModel();
        $submissions = array();
        foreach ($tasks as $task) {
            $submissions[] = $studentModel->getTaskSubmissionsCount($userId, $task['id']);
        }

        $_SESSION['submissions'] = $submissions;
        $_SESSION['assignmentId'] = $assignmentId;

        require('views/student/viewTasks.php');
        require('views/common/footer.php');
    }

    public function viewTests($taskId)
    {
        RedirectHelper::redirect(ROLE_STUDENT);

        $pageTitle = 'Tests';

        require('views/common/header.php');

        $taskModel = new TaskModel();
        $studentModel = new StudentModel();

        $_SESSION['taskTitle'] = $taskModel->getTaskTitle($taskId);
        $_SESSION['tests'] = $studentModel->getVisibleTests($taskId);
        $_SESSION['testCodingStyle'] = $studentModel->getTestCodingStyle($taskId);
        $_SESSION['taskId'] = $taskId;

        require('views/student/viewTests.php');
        require('views/common/footer.php');
    }

    public function addCode($taskId)
    {
        RedirectHelper::redirect(ROLE_STUDENT);

        $pageTitle = 'Add code';

        require('views/common/header.php');

        $userId = $_SESSION['userId'];
        $courseId = $_SESSION['courseId'];
        $assignmentId = $_SESSION['assignmentId'];

        $studentModel = new StudentModel();
        $taskModel = new TaskModel();

        $taskTitle = $taskModel->getTaskTitle($taskId);
        $_SESSION['taskTitle'] = $taskTitle;

        $submissions = $studentModel->getTaskSubmissionsCount($userId, $taskId);
        $_SESSION['submissions'] = $submissions;

        $dueDate = $studentModel->getTaskDueDate($taskId);
        $_SESSION['dueDate'] = $dueDate;

        $submissionLimit = $taskModel->getTaskTry($taskId);
        $_SESSION['submissionLimit'] = $submissionLimit;

        if (!empty($_FILES)) {
            $fileName = $_FILES["fileToUpload"]["name"];
            $fileType = $_FILES["fileToUpload"]["type"];
            $fileSize = $_FILES["fileToUpload"]["size"];
            $tempFile = $_FILES["fileToUpload"]["tmp_name"];

            $fileExtension = '.java';
            if ($fileType != "application/octet-stream" ||
                substr_compare($fileName, $fileExtension, -strlen($fileExtension)) !== 0) {
                $_SESSION['error_message'] = 'Please upload a Java file with extension "' . $fileExtension . '"!';
                header('Location: index.php?action=addCode&id=' . $taskId);
                exit();
            }

            $path = getcwd() . '/submissions/' . $courseId . '/' . $assignmentId . '/' . $taskId . '/' . $userId . '/'
                . ($submissions + 1);
            mkdir($path, 0777, true);

            $newFileName = $studentModel->getTaskClassName($taskId) . '.java';
            $fileNameWithDirectory = $path . '/' . $newFileName;

            if (move_uploaded_file($tempFile, $fileNameWithDirectory)) {
                if ($submissionLimit <= $submissions) {
                    $_SESSION['error_message'] = 'The maximum number of submissions has been reached.';
                    header('Location: index.php?action=addCode&id=' . $taskId);
                    exit();
                }

                if ($dueDate <= date("Y-m-d H:i:s", time())) {
                    $_SESSION['error_message'] = 'The due date has passed.';
                    header('Location: index.php?action=addCode&id=' . $taskId);
                    exit();
                }

                $submissionId = $studentModel->insertSubmission($userId, $taskId, $submissions + 1);
                if ($submissionId != null) {
                    $_SESSION['ok_message'] = 'File ' . $fileName . ' of size ' . $fileSize . ' bytes has been uploaded.';
                } else {
                    $_SESSION['error_message'] = 'Error uploading file ' . $fileName . '!';
                }

                $result = $studentModel->updateSubmission($submissionId, $userId, $taskId);
//                if ($result == null) {
//                    $_SESSION['error_message'] = 'Could not set ' . $newFileName . ' as final submission.';
//                }
            } else {
                $_SESSION['error_message'] = 'Error occurred during file upload!';
            }
            header('Location: index.php?action=addCode&id=' . $taskId);
            exit();
        }
        require('views/student/addCode.php');
        require('views/common/footer.php');
    }

    public function viewCode($taskId)
    {
        RedirectHelper::redirect(ROLE_STUDENT);

        $pageTitle = 'Submitted Code';

        $_SESSION['taskId'] = $taskId;

        $userId = $_SESSION['userId'];

        $studentModel = new StudentModel();
        $taskModel = new TaskModel();
        $_SESSION['submissions'] = $studentModel->getTaskSubmissions($taskId, $userId);
        $_SESSION['taskTitle'] = $taskModel->getTaskTitle($taskId);
        $_SESSION['checkSubmittedCode'] = $studentModel->checkIfStudentSubmittedCode($userId, $taskId);

        require('views/common/header.php');
        require('views/student/viewCode.php');
        require('views/common/footer.php');
    }

    public function downloadFile($taskId, $version, $final)
    {
        RedirectHelper::redirect(ROLE_STUDENT);

        $courseId = $_SESSION['courseId'];
        $assignmentId = $_SESSION['assignmentId'];
        $userId = $_SESSION['userId'];

        if (isset($_REQUEST["file"])) {
            $file = urldecode($_REQUEST["file"]);

            if (preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $file)) {
                if ($final == 0) {
                    $filepath = "./submissions/" . $courseId . '/' . $assignmentId . '/' . $taskId . '/' . $userId . '/' . $version . '/' . $file;
                } else {
                    $filepath = "./submissions/" . $courseId . '/' . $assignmentId . '/' . $taskId . '/' . $userId . '/' . $version . '/final/' . $file;
                }

                if (file_exists($filepath)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($filepath));
                    readfile($filepath);
                    exit;
                } else {
                    $_SESSION['error_message'] = 'The file does not exist!';
                    header('Location: index.php?action=viewCode&id=' . $taskId);
                    exit();
                }
            } else {
                $_SESSION['error_message'] = 'Invalid file name!';
                header('Location: index.php?action=viewCode&id=' . $taskId);
                exit();
            }
        }
        require('views/common/header.php');
        require('views/student/viewCode.php');
        require('views/common/footer.php');
    }

    public function setFinal($submissionId)
    {
        RedirectHelper::redirect(ROLE_STUDENT);

        $userId = $_SESSION['userId'];
        $taskId = $_SESSION['taskId'];

        $studentModel = new StudentModel();
        $result = $studentModel->setFinalSubmission($submissionId);
        if ($result == null) {
            $_SESSION['error_message'] = 'Could not set final submission.';
        }

        $studentModel->updateSubmission($submissionId, $userId, $taskId);

        header('Location: index.php?action=viewCode&id=' . $taskId);
    }

    public function viewTestOutput($submissionId)
    {
        RedirectHelper::redirect(ROLE_STUDENT);

        $pageTitle = 'View Test Output';

        $assignmentModel = new AssignmentModel();
        $studentModel = new StudentModel();
        $taskModel = new TaskModel();

        $navigationDetails = $studentModel->getNavigationDetailsTestOutput($submissionId);
        $_SESSION['courseId'] = $courseId = $navigationDetails[0];
        $_SESSION['courseTitle'] = $courseTitle = $navigationDetails[1];
        $_SESSION['assignmentId'] = $assignmentId = $navigationDetails[2];
        $_SESSION['assignmentTitle'] = $assignmentTitle = $navigationDetails[3];
        $_SESSION['taskId'] = $taskId = $navigationDetails[4];
        $_SESSION['taskTitle'] = $taskTitle = $navigationDetails[5];

        $taskId = $_SESSION['taskId'];
        $assignmentId = $_SESSION['assignmentId'];

        $_SESSION['taskTitle'] = $taskModel->getTaskTitle($taskId);
        $_SESSION['submissionTests'] = $studentModel->getSubmissionTestOutput($submissionId, $_SESSION['userId']);
        $_SESSION['checkGradesReleased'] = $assignmentModel->checkGradesReleased($assignmentId);

        require('views/common/header.php');
        require('views/student/viewTestOutput.php');
        require('views/common/footer.php');
    }
}