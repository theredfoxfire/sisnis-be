<?php

namespace App\Controller;

use App\Repository\StudentRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\ClassHistoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\UserRepository;

/**
 * Class StudentSiteController
 * @package App\Controller
 *
 * @Route(path="/api/student")
 */
class StudentController
{
    private $studentRepository;
    private $classRoomRepository;
    private $classHistoryRepository;
    private $userRepository;

    public function __construct(
        StudentRepository $studentRepository,
        UserRepository $userRepository,
        ClassRoomRepository $classRoomRepository,
        ClassHistoryRepository $classHistoryRepository
    )
    {
        $this->studentRepository = $studentRepository;
        $this->classRoomRepository = $classRoomRepository;
        $this->classHistoryRepository = $classHistoryRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/add", name="add_student", methods={"POST"})
     */
    public function addStudent(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->name) || empty($data->serial)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $classRoom = null;
        if (!empty($data->classRoom)) {
            $classRoom = $this->classRoomRepository->findOneBy(['id' => $data->classRoom]);
        }
        $lowerSerial = strtolower($data->serial);
        $userData = [
            'username' => strtolower($lowerSerial),
            'password' => '12345'.strtolower($lowerSerial),
            'email' => strtolower($lowerSerial).'@mail.com',
            'roles' => '["ROLE_STUDENT"]',
        ];
        $parentData = [
            'username' => 'wali'.strtolower($lowerSerial),
            'password' => 'wali12345',
            'email' => 'wali'.strtolower($lowerSerial).'@mail.com',
            'roles' => '["ROLE_PARENT"]',
        ];

        $userStudent = $this->userRepository->createUser((object) $userData, $encoder);
        $userParent = $this->userRepository->createUser((object) $parentData, $encoder);
        $this->studentRepository->saveStudent($data, $classRoom, $userStudent, $userParent);

        return new JsonResponse(['status' => 'Student added!'], Response::HTTP_OK);
    }

    /**
     * @Route("/get/{id}", name="get_one_student", methods={"GET"})
     */
    public function getOneStudent($id): JsonResponse
    {
        $student = $this->studentRepository->findOneBy(['id' => $id]);
        $classRoom = $student->getClassRoom();
        $data = [
            'id' => $student->getId(),
            'name' => $student->getName(),
            'serial' => $student->getSerial(),
            'classId' => $classRoom ? $classRoom->getId() : 0,
            'className' => $classRoom ? $classRoom->getName() : '',
            'gender' => $student->getGender(),
     'birthDay' => $student->getBirthDay(),
      'parentName' => $student->getParentName(),
      'city' => $student->getCity(),
      'parentAddress' => $student->getParentAddress(),
      'religion' => $student->getReligion(),
        ];

        return new JsonResponse(['student' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/add/class-room/{id}", name="add_student_class", methods={"PUT"})
     */
    public function addStudentClassRoom($id, Request $request): JsonResponse
    {
        $student = $this->studentRepository->findOneBy(['id' => $id]);
        $data = (object) json_decode($request->getContent(), true);
        if (empty($data->classRoomId)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $student->setClassRoom($this->classRoomRepository->findOneBy(['id' => $data->classRoomId]));
        $this->studentRepository->updateStudentClassRoom($student, $data);

        return new JsonResponse(['status' => 'student updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_students", methods={"GET"})
     */
    public function getAllStudents(Request $request): JsonResponse
    {
        $currentPage = $request->query->get('page');
        $pageItems = $request->query->get('pageItems');
        $name = $request->query->get('name');
        $haveClass = $request->query->get('haveClass');
        $start = ($currentPage - 1) * $pageItems;
        $students = $this->studentRepository->getAllStudents($start, $pageItems, $name, $haveClass);
        $data = [];

        foreach ($students->data as $student) {
            $classRoom = $student->getClassRoom();
            $data[] = [
                'id' => $student->getId(),
                'name' => $student->getName(),
                'serial' => $student->getSerial(),
                'classId' => $classRoom ? $classRoom->getId() : 0,
                'className' => $classRoom ? $classRoom->getName() : '',
            ];
        }

        return new JsonResponse(['students' => $data, 'totals' => $students->totals], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_student", methods={"PUT"})
     */
    public function updateStudent($id, Request $request): JsonResponse
    {
        $student = $this->studentRepository->findOneBy(['id' => $id]);
        $data = (object)json_decode($request->getContent(), true);
        $classRoom = null;
        if (!empty($data->classRoom)) {
            $classRoom = $this->classRoomRepository->findOneBy(['id' => $data->classRoom]);
            $student->getClassRoom() ? $this->classHistoryRepository->saveClassHistory($student, $student->getClassRoom()) : false;
            $student->setClassRoom($classRoom);
        }

        $this->studentRepository->updateStudent($student, $data);

        return new JsonResponse(['status' => 'student updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="delete_student", methods={"DELETE"})
     */
    public function deleteStudent($id): JsonResponse
    {
        $student = $this->studentRepository->findOneBy(['id' => $id]);

        $this->studentRepository->removeStudent($student);

        return new JsonResponse(['status' => 'student deleted'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/class/{id}", name="delete_student_class", methods={"DELETE"})
     */
    public function deleteStudentClass($id): JsonResponse
    {
        $student = $this->studentRepository->findOneBy(['id' => $id]);
        $this->classHistoryRepository->saveClassHistory($student, $student->getClassRoom());
        $student->setClassRoom(null);
        $this->studentRepository->updateStudentClassRoom($student);

        return new JsonResponse(['status' => 'class-room deleted'], Response::HTTP_OK);
    }
}
