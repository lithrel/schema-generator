<?php

declare(strict_types=1);

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * To Add an Object or Link to Something.
 *
 * @see http://www.w3.org/ns/activitystreams#Add Documentation on Schema.org
 *
 * @ORM\Entity
 * @ApiResource(iri="http://www.w3.org/ns/activitystreams#Add")
 */
class Add
{
    /**
     * @var string|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @Assert\Uuid
     */
    private $id;

    /**
     * @var string|null Subproperty of as:attributedTo that identifies the primary actor
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/actor")
     */
    private $actor;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/attachment")
     */
    private $attachment;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/attachments")
     */
    private $attachment;

    /**
     * @var string|null Identifies the author of an object. Deprecated. Use as:attributedTo instead
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/author")
     */
    private $author;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/bcc")
     */
    private $bcc;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/bto")
     */
    private $bto;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/cc")
     */
    private $cc;

    /**
     * @var string|null Specifies the context within which an object exists or an activity was performed
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/context")
     */
    private $context;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/generator")
     */
    private $generator;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/icon")
     */
    private $icon;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/image")
     */
    private $image;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/inReplyTo")
     */
    private $inReplyTo;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/location")
     */
    private $location;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/provider")
     */
    private $provider;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/replies")
     */
    private $reply;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/result")
     */
    private $result;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/audience")
     */
    private $audience;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/tag")
     */
    private $tag;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/tags")
     */
    private $tag;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/target")
     */
    private $target;

    /**
     * @var string|null for certain activities, specifies the entity from which the action is directed
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/origin")
     */
    private $origin;

    /**
     * @var string|null Indentifies an object used (or to be used) to complete an activity
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/instrument")
     */
    private $instrument;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/to")
     */
    private $to;

    /**
     * @var string|null Specifies a link to a specific representation of the Object
     *
     * @ORM\Column(type="text",nullable=true)
     * @ApiProperty(iri="http://schema.org/url")
     */
    private $url;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setActor(?string $actor): void
    {
        $this->actor = $actor;
    }

    public function getActor(): ?string
    {
        return $this->actor;
    }

    public function setAttachment(?string $attachment): void
    {
        $this->attachment = $attachment;
    }

    public function getAttachment(): ?string
    {
        return $this->attachment;
    }

    public function setAttachment(?string $attachment): void
    {
        $this->attachment = $attachment;
    }

    public function getAttachment(): ?string
    {
        return $this->attachment;
    }

    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setBcc(?string $bcc): void
    {
        $this->bcc = $bcc;
    }

    public function getBcc(): ?string
    {
        return $this->bcc;
    }

    public function setBto(?string $bto): void
    {
        $this->bto = $bto;
    }

    public function getBto(): ?string
    {
        return $this->bto;
    }

    public function setCc(?string $cc): void
    {
        $this->cc = $cc;
    }

    public function getCc(): ?string
    {
        return $this->cc;
    }

    public function setContext(?string $context): void
    {
        $this->context = $context;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setGenerator(?string $generator): void
    {
        $this->generator = $generator;
    }

    public function getGenerator(): ?string
    {
        return $this->generator;
    }

    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setInReplyTo(?string $inReplyTo): void
    {
        $this->inReplyTo = $inReplyTo;
    }

    public function getInReplyTo(): ?string
    {
        return $this->inReplyTo;
    }

    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setProvider(?string $provider): void
    {
        $this->provider = $provider;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setReply(?string $reply): void
    {
        $this->reply = $reply;
    }

    public function getReply(): ?string
    {
        return $this->reply;
    }

    public function setResult(?string $result): void
    {
        $this->result = $result;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setAudience(?string $audience): void
    {
        $this->audience = $audience;
    }

    public function getAudience(): ?string
    {
        return $this->audience;
    }

    public function setTag(?string $tag): void
    {
        $this->tag = $tag;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(?string $tag): void
    {
        $this->tag = $tag;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTarget(?string $target): void
    {
        $this->target = $target;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setOrigin(?string $origin): void
    {
        $this->origin = $origin;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setInstrument(?string $instrument): void
    {
        $this->instrument = $instrument;
    }

    public function getInstrument(): ?string
    {
        return $this->instrument;
    }

    public function setTo(?string $to): void
    {
        $this->to = $to;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }
}
