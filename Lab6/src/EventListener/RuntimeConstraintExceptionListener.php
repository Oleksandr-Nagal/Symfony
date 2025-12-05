<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\ConstraintViolationList;
use Throwable;

/**
 * Цей клас слухає подію kernel.exception.
 * Він перехоплює ВСІ помилки в додатку і форматує їх
 * у єдиний JSON-формат, як вимагала Лабораторна 3.
 */
class RuntimeConstraintExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $code = $this->getCode($exception);
        $errors = $this->getErrors($exception);

        $event->setResponse(new JsonResponse([
            "data" => [
                "code" => $code,
                "errors" => $errors
            ]
        ], $code));
    }

    /**
     * @param Throwable $exception
     * @return int
     */
    public function getCode(Throwable $exception): int
    {
        if (method_exists($exception, "getStatusCode")) {
            return array_key_exists($exception->getStatusCode(),
                Response::$statusTexts) ? $exception->getStatusCode() :
                Response::HTTP_UNPROCESSABLE_ENTITY;
        }
        return array_key_exists($exception->getCode(), Response::$statusTexts) ?
            $exception->getCode() : Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    /**
     * @param Throwable $exception
     * @return array
     */
    public function getErrors(Throwable $exception): array
    {
        $errors = [];
        if (method_exists($exception, "getConstraintViolationList")) {
            return
                $this->getAssociativeErrorsForConstraintViolationList($exception->getConstraintViolationList(), $errors);
        }
        if ($tmpErrors = json_decode($exception->getMessage(), true)) {
            return $this->getAssociativeErrors($tmpErrors["data"]["errors"] ??
                $tmpErrors, $errors);
        }
        $errors[] = [$exception->getMessage()];
        return $errors;
    }

    /**
     * @param array $tmpErrors
     * @param array $errors
     * @return array
     */
    public function getAssociativeErrors(array $tmpErrors, array $errors): array
    {
        foreach ($tmpErrors as $key => $error) {
            if (is_array($error)) {
                $errors[$key] = $this->getAssociativeErrors($error, $errors);
            } else {
                $errors[$key] = $error;
            }
        }
        return $errors;
    }

    /**
     * @param ConstraintViolationList $list
     * @param array $errors
     * @return array
     */
    public function
    getAssociativeErrorsForConstraintViolationList(ConstraintViolationList $list, array $errors): array
    {
        foreach ($list as $error) {
            $propertyPath = $error->getPropertyPath();
            $key = str_replace(['[', ']'], '', $propertyPath);
            $errors[$key] = $error->getMessage();
        }
        return $errors;
    }
}