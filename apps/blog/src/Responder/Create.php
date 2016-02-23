<?php
namespace Blog\Responder;

use Neat\Base\AbstractResponder;

/**
 * Responder: create.
 */
class Create extends AbstractResponder
{
    /**
     * @return Response
     */
	public function __invoke ()
    {
        // is there an ID on the blog instance?
        if ($this->data->blog->id) {
            // yes, which means it was saved already.
            // redirect to editing.
            $this->response->setRedirect('/blog/edit/{$blog->id}');
        } else {
            // no, which means it has not been saved yet.
            // show the creation form with the current response data.
            $this->response->setContent($this->view->render(
                'create.html.php',
                $this->data
            ));
        }
    }
}