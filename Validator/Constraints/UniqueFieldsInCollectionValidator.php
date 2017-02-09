<?php

namespace Tisseo\EndivBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UniqueFieldsInCollectionValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueFieldsInCollection) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\UniqueFieldsInCollection');
        }

        if (null === $value) {
            return;
        }

        if (!is_array($value) && !($value instanceof \Traversable && $value instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($value, 'array or Traversable and ArrayAccess');
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        $check = array();
        $stop = false;
        foreach ($value as $data) {
            foreach ($constraint->fields as $field) {
                $check[$field][] = $accessor->getValue($data, $field);

                if (count(array_unique($check[$field])) < count($check[$field])) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('%string%', $field)
                        ->addViolation();
                    $stop = true;
                    break;
                }
            }
            if ($stop) {
                break;
            }
        }
    }
}
