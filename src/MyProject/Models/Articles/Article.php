<?php
namespace MyProject\Models\Articles;

use MyProject\Models\Users\User;
use Vendor\Models\ActiveRecordEntity;
use MyProject\Exceptions\InvalidArgumentException;

class Article extends ActiveRecordEntity
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $text;

    /** @var int */
    protected $authorId;

    /** @var string */
    protected $createdAt;
    
    /** 
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
    
    /** 
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * @return User
     */
    public function getAuthor(): User {
        return User::getById($this->authorId);
    }

    /**
     * @param string $name
     */
    public function setName(string $name) {
        $this->name = $name;
    }

    /**
     * $param User $author
     */
    public function setAuthor(User $author) {
        $this->authorId = $author->getId();
    }
    /**
     * @param string $text
     */
    public function setText(string $text) {
        $this->text = $text;
    }
    public static function createFromArray(array $fields, User $author): Article {
        if (empty($fields['name'])) {
            throw new InvalidArgumentException('Не передано название статьи');
        }
        if (empty($fields['text'])) {
            throw new InvalidArgumentException('Не передан текст статьи');
        }
        $article = new Article();
        $article->setAuthor($author);
        $article->setName($fields['name']);
        $article->setText($fields['text']);
        $article->save();
        return $article;
    }
    protected static function getTableName(): string {
        return 'articles';
    }
}