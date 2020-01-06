<?php


namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResetPasswordAction
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * ResetPasswordAction constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function __invoke(User $data)
    {
        // $reset = new ResetPasswordAction();
        // $reset();
//        var_dump(
//            $data->getNewPassword(),
//            $data->getNewRetypedPassword(),
//            $data->getOldPassword()
//            //$data->getRetypedPassword()
//        ); die();

        // Validation is only called after we return
        // the data from this action
        $this->validator->validate($data);

        return $data;
    }
}