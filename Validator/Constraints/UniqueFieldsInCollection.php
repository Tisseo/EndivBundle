<?

namespace Tisseo\EndivBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class UniqueFieldsInCollection extends Constraint
{
    public $message = 'The fields {{ fields }} must be unique in this collection';

    public $fields = array();

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        // no known options set? $options is the fields array
        if (is_array($options)
            && !array_intersect(array_keys($options), array('message', 'fields'))) {
            $options = array('fields' => $options);
        }

        parent::__construct($options);
    }

    public function getRequiredOptions()
    {
        return array('fields');
    }

    protected function getCompositeOption()
    {
        return 'fields';
    }
}
