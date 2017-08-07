<?php
/**
-------------------------
GNU GPL COPYRIGHT NOTICES
-------------------------
This file is part of FlexicaCMS.

FlexicaCMS is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

FlexicaCMS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with FlexicaCMS.  If not, see <http://www.gnu.org/licenses/>.*/

/**
 * $Id$
 *
 * @author FlexicaCMS team <contact@flexicacms.com>
 * @link http://www.flexicacms.com/
 * @copyright Copyright &copy; 2009-2010 Gia Han Online Solutions Ltd.
 * @license http://www.flexicacms.com/license.html
 */
class CommentService extends FServiceBase
{
    /**
    * Create a new comment
    *
    * @param array
    */
    public function create($params){
        $comment = $this->getModel($params['Comment'], 'Comment');
        $needApproval = $this->getParam($params ,'commentNeedApproval', true);

        if($needApproval)
			$comment->status=Comment::STATUS_PENDING;
		else
			$comment->status=Comment::STATUS_APPROVED;

		$comment->id = null;
        if (!$comment->save())
            $this->result->addError('comment', 'Invalid data or cannot save comment into database');

        $this->result->processed('comment', $comment);
    }

    /**
    * Update an existed comment
    *
    * @param array
    */
    public function update($params){
        $comment = $this->getModel($params['Comment'], 'Comment');

		$comment->isNewRecord = false;
        if (!$comment->update(array('author', 'email', 'url', 'content')))
            $this->result->addError('comment', 'Invalid data or cannot save comment into database');

        $this->result->processed('comment', $comment);
    }

    /**
	 * Approves a comment.
	 */
	public function approve($params)
	{
		$commentId = $this->getParam($params, 'commentId', 0);
		Comment::model()->updateByPk($commentId, array('status' => Comment::STATUS_APPROVED));
	}

	/**
    * Delete 1 comment by commentId
    *
    * @param array
    */
    public function delete($params){
    	$commentId = $this->getParam($params, 'commentId', 0);
        Comment::model()->deleteAll('id=:commentId', array(':commentId' => $commentId));
    }

	/**
    * List all comments
    *
    * @param array
    */
    public function search($params) {
        $dataProvider=new CActiveDataProvider('Comment', array(
			'criteria'=>array(
				'with'=>'post',
				'order'=>'t.status, t.create_time DESC',
			),
		));

        $this->result->processed('dataProvider', $dataProvider);
    }

    /**
     * find recent comments with limit
	 * @param array
	 */
	public function findRecentComments($params)
	{
		$limit = $this->getParam($params, 'limit', 0);
		$comments = Comment::model()->with('post')->findAll(array(
			'condition'=>'t.status='.Comment::STATUS_APPROVED,
			'order'=>'t.create_time DESC',
			'limit'=>$limit,
		));
		$this->result->processed('comments', $comments);
	}

	/**
	 * get number of pending comments
	 * @param array
	 */
	public function getPendingCommentCount($params)
	{
		$count = Comment::model()->count('status='.Comment::STATUS_PENDING);
		$this->result->processed('pendingCommentCount', $count);
	}
}
?>