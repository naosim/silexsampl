<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2013 Bartosz Maciaszek <bartosz.maciaszek@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package    Rtm.Service
 * @author     Bartosz Maciaszek <bartosz.maciaszek@gmail.com>
 * @copyright  2013 Bartosz Maciaszek.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 */

namespace Rtm\Service;

use Rtm\Rtm;

class Contacts extends AbstractService
{
    /**
     * Adds a new contact. $contact should be a username or email address of a Remember The Milk user.
     * @param string  $contact
     * @param integer $timeline
     * @return DataContainer
     * @link https://www.rememberthemilk.com/services/api/methods/rtm.contacts.add.rtm
     */
    public function add($contact, $timeline = null)
    {
        $params = array(
            'contact'  => $contact,
            'timeline' => $timeline === null ? $this->getTimeline() : $timeline
        );

        return $this->rtm->call(Rtm::METHOD_CONTACTS_ADD, $params);
    }

    /**
     * Deletes a contact.
     * @param  integer $id
     * @param  integer $timeline
     * @return DataContainer
     * @link https://www.rememberthemilk.com/services/api/methods/rtm.contacts.delete.rtm
     */
    public function delete($id, $timeline = null)
    {
        $params = array(
            'contact_id' => $id,
            'timeline'   => $timeline === null ? $this->getTimeline() : $timeline
        );

        return $this->rtm->call(Rtm::METHOD_CONTACTS_DELETE, $params);
    }

    /**
     * Retrieves a list of contacts.
     * @return DataContainer
     * @link https://www.rememberthemilk.com/services/api/methods/rtm.contacts.getList.rtm
     */
    public function getList()
    {
        return $this->rtm->call(Rtm::METHOD_CONTACTS_GET_LIST)->getContacts();
    }
}
