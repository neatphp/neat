<?php
namespace Blog\Action;

use Neat\Base\AbstractAction;

/**
 * Index action.
 */
class Create extends AbstractAction
{
    /**
     * @return Response
     */
	public function __invoke ()
	{
        // is this a POST request?
        if ($this->request->isPost()) {

            // yes, retain incoming data
            $data = $this->request->getPost('blog');

            // create a blog post instance
            $blog = $this->blog_model->newInstance($data);

            // is the new instance valid?
            if ($blog->isValid()) {
                $blog->save();
            }

        } else {
            // not a POST request, use default values
            $blog = $this->blog_model->getDefault();
        }

        // set data into the response
        $this->responder->setData(array('blog' => $blog));
        $this->responder->__invoke();
    }
}