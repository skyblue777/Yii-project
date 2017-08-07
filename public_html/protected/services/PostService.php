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
class PostService extends FServiceBase
{
    /**
    * Create a new post
    *
    * @param array
    */
    public function create($params){
        $post = $this->getModel($params['Post'], 'Post');

		$post->id = null;
        $this->result->processed('post', $post);
        if (!$post->save())
            $this->result->addError('post', 'Invalid data or cannot save post into database');
    }

    /**
    * Update an existed post
    *
    * @param array
    */
    public function update($params){
        $post = $this->getModel($params['Post'], 'Post');
        $post->isNewRecord = false;
        if (!$post->update())
            $this->result->addError('post', 'Invalid data or cannot save post into database');

		$this->result->processed('post', $post);
    }

    /**
    * Delete 1 post by postId
    *
    * @param array
    */
    public function delete($params){
    	$postId = $this->getParam($params, 'postId', 0);
		$post = Post::model()->findByPk($postId);
		if (is_null($post)) {
			return $this->result->fail(0, 'Invalid Post Id');
		}

		if (!$post->delete()) {
			return $this->result->fail(0, 'Error. Cannot delete this post');
		}
    }

    public function search($params) {
        $criteria=new CDbCriteria(array(
            'condition'=>'status='.Post::STATUS_PUBLISHED,
            'order'=>'update_time DESC',
            'with'=>'commentCount',
        ));

        $tag = $this->getParam($params, 'tag', null);
        if($tag != null)
            $criteria->addSearchCondition('tags', $tag);

        $dataProvider=new CActiveDataProvider('Post', array(
            'pagination'=>array(
                'pageSize'=>Yii::app()->params['postsPerPage'],
            ),
            'criteria'=>$criteria,
        ));

        $this->result->processed('dataProvider', $dataProvider);
    }

    public function suggestTags($params) {
        $keyword = $this->getParam($params, 'q', '');
        if ($keyword != '') {
            $tags=Tag::model()->suggestTags($keyword);
            if($tags!==array())
                return $this->result->processed('tags', implode("\n",$tags));
        }

        return $this->result->processed('tags','');
    }
}
?>