<?php
/**
 * @title          Comment Core Model Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 * @version        1.0
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;

class CommentCoreModel extends Model
{
    const CACHE_GROUP = 'db/sys/mod/comment';
    const CACHE_TIME = 345600;
    const CREATED = 'createdDate';
    const UPDATED = 'updatedDate';

    /**
     * @param string $sTable
     * @param string $sApproved
     * @param string $sOrder
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array
     */
    public function gets($sTable, $sApproved = '1', $sOrder = self::UPDATED, $iOffset = 0, $iLimit = 500)
    {
        $sTable = CommentCore::checkTable($sTable);
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $rStmt = Db::getInstance()->prepare('SELECT c.*, m.username, m.firstName, m.sex FROM' .
            Db::prefix('comments_' . $sTable) . ' AS c LEFT JOIN' . Db::prefix(DbTableName::MEMBER) .
            'AS m ON c.sender = m.profileId WHERE c.approved = :approved ORDER BY ' .
            $sOrder . ' DESC LIMIT :offset, :limit'
        );

        $rStmt->bindParam(':approved', $sApproved, PDO::PARAM_STR);
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();
        $oData = $rStmt->fetchAll(PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $oData;
    }

    /**
     * @param int $iRecipientId
     * @param string $sApproved
     * @param int $iOffset
     * @param int $iLimit
     * @param string $sTable
     *
     * @return array
     */
    public function read($iRecipientId, $sApproved, $iOffset, $iLimit, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlRecipientId = !empty($iRecipientId) ? 'c.recipient =:recipient AND' : '';

        $rStmt = Db::getInstance()->prepare('SELECT c.*, m.username, m.firstName, m.sex FROM' .
            Db::prefix('comments_' . $sTable) . ' AS c LEFT JOIN' . Db::prefix(DbTableName::MEMBER) .
            'AS m ON c.sender = m.profileId WHERE ' . $sSqlRecipientId . ' c.approved =:approved ORDER BY c.createdDate DESC LIMIT :offset, :limit');

        if (!empty($iRecipientId)) {
            $rStmt->bindParam(':recipient', $iRecipientId, PDO::PARAM_INT);
        }
        $rStmt->bindParam(':approved', $sApproved, PDO::PARAM_STR);
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();
        $oData = $rStmt->fetchAll(PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $oData;
    }

    /**
     * @param int $iRecipientId
     * @param string $sTable
     *
     * @return array|bool|float|int|object|string
     */
    public function total($iRecipientId, $sTable)
    {
        $this->cache->start(static::CACHE_GROUP, 'total' . $iRecipientId . $sTable, static::CACHE_TIME);

        if (!$iData = $this->cache->get()) {
            $sTable = CommentCore::checkTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT COUNT(commentId) AS totalComments FROM' . Db::prefix('comments_' . $sTable) . ' WHERE recipient = :recipient');
            $rStmt->bindParam(':recipient', $iRecipientId);
            $rStmt->execute();
            $oRow = $rStmt->fetch(PDO::FETCH_OBJ);
            Db::free($rStmt);
            $iData = (int)$oRow->totalComments;
            unset($oRow);
            $this->cache->put($iData);
        }

        return $iData;
    }

    /**
     * Delete a comment.
     *
     * @param int $iRecipientId The Comment Recipient ID.
     * @param string $sTable The Comment Table.
     *
     * @return bool Returns TRUE on success, FALSE on failure.
     */
    public static function deleteRecipient($iRecipientId, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);

        $iRecipientId = (int)$iRecipientId;
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('comments_' . $sTable) . 'WHERE recipient = :recipient');
        $rStmt->bindValue(':recipient', $iRecipientId, PDO::PARAM_INT);

        return $rStmt->execute();
    }
}
