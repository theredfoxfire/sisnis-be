<?php

namespace App\Controller;

use App\Repository\TeacherRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\SubjectRepository;
use App\Repository\AcademicYearRepository;
use App\Repository\TeacherClassToSubjectRepository;
use App\Entity\TeacherClassToSubject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\UserRepository;

/**
 * Class TeacherSiteController
 * @package App\Controller
 *
 * @Route(path="/api/teacher")
 */
class TeacherController
{
    private $teacherRepository;
    private $classRoomRepository;
    private $subjectRepository;
    private $teacherMapRepository;
    private $academicYearRepository;
    private $userRepository;

    public function __construct(AcademicYearRepository $academicYearRepository,
    UserRepository $userRepository,
    TeacherClassToSubjectRepository $teacherMapRepository,SubjectRepository $subjectRepository, TeacherRepository $teacherRepository, ClassRoomRepository $classRoomRepository)
    {
        $this->teacherRepository = $teacherRepository;
        $this->classRoomRepository = $classRoomRepository;
        $this->subjectRepository = $subjectRepository;
        $this->teacherMapRepository = $teacherMapRepository;
        $this->academicYearRepository = $academicYearRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/add", name="add_teacher", methods={"POST"})
     */
    public function addTeacher(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->name) || empty($data->serial)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $lowerSerial = strtolower($data->serial);
        $userData = [
                'username' => strtolower($lowerSerial),
                'password' => '12345'.strtolower($lowerSerial),
                'email' => strtolower($lowerSerial).'@mail.com',
                'roles' => '["ROLE_TEACHER"]',
            ];
        $userTeacher = $this->userRepository->createUser((object) $userData, $encoder);
        $this->teacherRepository->saveTeacher($data, $userTeacher);

        return new JsonResponse(['status' => 'Teacher added!'], Response::HTTP_OK);
    }

    /**
     * @Route("/get/{id}", name="get_one_teacher", methods={"GET"})
     */
    public function getOneTeacher($id): JsonResponse
    {
        $teacher = $this->teacherRepository->findOneBy(['id' => $id]);
        if (empty($teacher)) {
            throw new NotFoundHttpException('Entity not found!');
        }
        $classSubjects = [];
        $guardianClass = [];
        foreach ($teacher->getTeacherClassToSubjects() as $key => $value) {
            if (!$value->getIsDeleted()) {
                $classSubjects[] = [
                    'classToSubjectId' => $value->getId(),
                    'classRoom' => $value->getClassRoom()->toArray(),
                    'subject' => $value->getSubject()->toArray(),
                    'kkm' => $value->getPassingPoint() ?? 0,
                    'yearId' => empty($value->getAcademicYear()) ? "" : $value->getAcademicYear()->getId(),
                    'year' => empty($value->getAcademicYear()) ? "" :  $value->getAcademicYear()->getYear(),
                ];
            }
        }
        foreach ($teacher->getGuardianClass() as $key => $class) {
            $guardianClass[] = $class->toArray();
        }

        $data = [
            'id' => $teacher->getId(),
            'name' => $teacher->getName(),
            'serial' => $teacher->getSerial(),
            'classToSubjects' => $classSubjects,
            'guardianClass' => $guardianClass,
        ];

        return new JsonResponse(['teacher' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_teachers", methods={"GET"})
     */
    public function getAllTeachers(): JsonResponse
    {
        $teachers = $this->teacherRepository->getAllTeacher();
        $data = [];
        foreach ($teachers as $teacher) {
            $guardianClass = [];
            foreach ($teacher->getGuardianClass() as $key => $class) {
                $guardianClass[] = $class->toArray();
            }

            $data[] = [
                'id' => $teacher->getId(),
                'name' => $teacher->getName(),
                'serial' => $teacher->getSerial(),
                'guardianClass' => $guardianClass,
            ];
        }

        return new JsonResponse(['teachers' => $data], Response::HTTP_OK);
    }
    /**
     * @Route("/get-all/subject", name="get_all_teachers_subject", methods={"GET"})
     */
    public function getAllTeacherSubject(): JsonResponse
    {
        $teachers = $this->teacherMapRepository->findAll();
        $data = [];
        foreach ($teachers as $item) {
          $subject = $item->getSubject();
          $classRoom = $item->getClassRoom();
          $teacher = $item->getTeacher();
            $data[] = [
                'id' => $item->getId(),
                'subject' => $subject->toArray() ?? [],
                'classRoom' => $classRoom->toArray() ?? [],
                'teacher' => $teacher->toArray() ?? [],
            ];
        }

        return new JsonResponse(['teacherSubject' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_teacher", methods={"PUT"})
     */
    public function updateTeacher($id, Request $request): JsonResponse
    {
        $teacher = $this->teacherRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        $this->teacherRepository->updateTeacher($teacher, $data);

        return new JsonResponse(['status' => 'teacher updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="delete_teacher", methods={"DELETE"})
     */
    public function deleteTeacher($id): JsonResponse
    {
        $teacher = $this->teacherRepository->findOneBy(['id' => $id]);

        $this->teacherRepository->removeTeacher($teacher);

        return new JsonResponse(['status' => 'teacher deleted'], Response::HTTP_OK);
    }

    /**
     * @Route("/add/class-room/{id}", name="add_teacher_class", methods={"PUT"})
     */
    public function addTeacherClassRoom($id, Request $request): JsonResponse
    {
        $data = (object) json_decode($request->getContent(), true);
        if (empty($data->classRoomId) || empty($data->subjectId) || empty($data->year)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $teacherMap = new TeacherClassToSubject();
        $teacherMap->setPassingPoint($data->kkm ?? 0);
        $teacherMap->setClassRoom($this->classRoomRepository->findOneBy(['id' => $data->classRoomId]));
        $teacherMap->setSubject($this->subjectRepository->findOneBy(['id' => $data->subjectId]));
        $teacherMap->setTeacher($this->teacherRepository->findOneBy(['id' => $id]));
        $teacherMap->setAcademicYear($this->academicYearRepository->findOneBy(['id' => $data->year]));
        $this->teacherMapRepository->updateTeacherClassRoom($teacherMap);

        return new JsonResponse(['status' => 'teacher updated!'], Response::HTTP_OK);
    }
    
    /**
     * @Route("/update/class-room/{id}/{teacherSubjectID}", name="update_teacher_class", methods={"PUT"})
     */
    public function updateTeacherClassRoom($id, $teacherSubjectID, Request $request): JsonResponse
    {
        $data = (object) json_decode($request->getContent(), true);
        if (empty($data->classRoomId) || empty($data->subjectId) || empty($data->year)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $teacherMap = $this->teacherMapRepository->findOneById($teacherSubjectID);
        $teacherMap->setPassingPoint($data->kkm ?? 0);
        $teacherMap->setClassRoom($this->classRoomRepository->findOneBy(['id' => $data->classRoomId]));
        $teacherMap->setSubject($this->subjectRepository->findOneBy(['id' => $data->subjectId]));
        $teacherMap->setTeacher($this->teacherRepository->findOneBy(['id' => $id]));
        $teacherMap->setAcademicYear($this->academicYearRepository->findOneBy(['id' => $data->year]));
        $this->teacherMapRepository->updateTeacherClassRoom($teacherMap);

        return new JsonResponse(['status' => 'teacher updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/class/{teacherMapId}", name="delete_teacher_class", methods={"DELETE"})
     */
    public function deleteTeacherClass($teacherMapId): JsonResponse
    {
        $teacherMap = $this->teacherMapRepository->findOneBy(['id' => $teacherMapId]);
        $this->teacherMapRepository->removeTeacherMap($teacherMap);

        return new JsonResponse(['status' => 'teacher-map deleted'], Response::HTTP_OK);
    }
}
