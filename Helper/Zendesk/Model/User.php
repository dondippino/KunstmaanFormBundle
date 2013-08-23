<?php

namespace Kunstmaan\FormBundle\Helper\Zendesk\Model;
use JMS\Serializer\Annotation as Serializer;

/**
 * Represents a Zendesk user
 *
 * @author Derek Clapham <derek.clapham@photomerchant.net>
 *
 * @Serializer\ExclusionPolicy("none")
 */
class User extends BaseModel
{


    /**
     * The name of the user.
     *
     * @var string $name
     *
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * A unique id you can set on a user.
     *
     * @var string $externalId
     *
     * @Serializer\SerializedName("external_id")
     * @Serializer\Type("string")
     */
    private $externalId;

    /**
     * Agents can have an alias that is displayed to end-users.
     *
     * @var string $alias
     *
     * @Serializer\Type("string")
     */
    private $alias;

    /**
     * The time the user was created.
     *
     * @var \DateTime $createdAt
     *
     * @Serializer\SerializedName("created_at")
     * @Serializer\Type("DateTime")
     */
    private $createdAt;

    /**
     * The time of the last update of the user.
     *
     * @var \DateTime $updatedAt
     *
     * @Serializer\SerializedName("updated_at")
     * @Serializer\Type("DateTime")
     */
    private $updatedAt;

    /**
     * Users that have been deleted will have the value false here.
     *
     * @var boolean $active
     *
     * @Serializer\Type("boolean")
     */
    private $active;

    /**
     * Zendesk has verified that this user is who he says he is.
     *
     * @var boolean $verified
     *
     * @Serializer\Type("boolean")
     */
    private $verified;

    /**
     * If this user is shared from a different Zendesk, ticket sharing accounts only.
     *
     * @var boolean $shared
     *
     * @Serializer\Type("boolean")
     */
    private $shared;

    /**
     * The language identifier for this user.
     *
     * @var integer $localeId
     *
     * @Serializer\SerializedName("locale_id")
     * @Serializer\Type("integer")
     */
    private $localeId;

    /**
     * The time-zone of this user.
     *
     * @var string $timezone
     *
     * @Serializer\Type("string")
     */
    private $timezone;

    /**
     * A time-stamp of the last time this user logged in to Zendesk.
     *
     * @var \DateTime $lastLoginAt
     *
     * @Serializer\SerializedName("last_login_at")
     * @Serializer\Type("DateTime")
     */
    private $lastLoginAt;

    /**
     * The primary email address of this user.
     *
     * @var string $email
     *
     * @Serializer\Type("string")
     */
    private $email;

    /**
     * The primary phone number of this user.
     *
     * @var string $phone
     *
     * @Serializer\Type("string")
     */
    private $phone;

    /**
     * Array of user identities (e.g. email and Twitter) associated with this user.
     * See http://developer.zendesk.com/documentation/rest_api/user_identities.html
     *
     * @var Array $identities
     *
     * @Serializer\Type("array")
     */
    private $identities;

    /**
     * The signature of this user. Only agents and admins can have signatures.
     *
     * @var string $signature
     *
     * @Serializer\Type("string")
     */
    private $signature;

    /**
     * In this field you can store any details about the user. e.g. the address.
     *
     * @var string $details
     *
     * @Serializer\Type("string")
     */
    private $details;

    /**
     * In this field you can store any notes you have about the user.
     *
     * @var string $notes
     *
     * @Serializer\Type("string")
     */
    private $notes;

    /**
     * The id of the organization this user is associated with
     * @var integer $organizationId
     *
     * @Serializer\SerializedName("organization_id")
     * @Serializer\Type("integer")
     */
    private $organizationId;

    /**
     * The role of the user. Possible values: "end-user", "agent", "admin"
     * @var string $role
     *
     * @Serializer\Type("string")
     */
    private $role = 'end-user';

    /**
     * A custom role on the user if the user is an agent on the entreprise plan
     * @var integer $customRoleId
     *
     * @Serializer\SerializedName("custom_role_id")
     * @Serializer\Type("integer")
     */
    private $customRoleId;

    /**
     * Designates whether this user has forum moderation capabilities
     * @var boolean $moderator
     *
     * @Serializer\Type("boolean")
     */
    private $moderator;

    /**
     * Specified which tickets this user has access to. Possible
     * values are: "organization", "groups", "assigned", "requested", null
     * @var string $ticketRestriction
     *
     * @Serializer\SerializedName("ticket_restriction")
     * @Serializer\Type("string")
     */
    private $ticketRestriction;

    /**
     * true if this user only can create private comments
     * @var boolean $onlyPrivateComments
     *
     * @Serializer\SerializedName("only_private_comments")
     * @Serializer\Type("boolean")
     */
    private $onlyPrivateComments;

    /**
     * The tags of the user. Only present if your account has user tagging enabled
     * @var Array $tags
     *
     * @Serializer\Type("array")
     */
    private $tags;

    /**
     * Tickets from suspended users are also suspended, and these users cannot log in to the end-user portal.
     *
     * @var boolean $suspended
     *
     * @Serializer\Type("boolean")
     */
    private $suspended;


    /**
     *
     * @var array $customFields
     *
     * @Serializer\SerializedName("custom_fields")
     * @Serializer\Type("array")
     */
    private $customFields;





    /**
     * @return string The $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string The $externalId
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     *
     * @return $this
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @return string The $alias
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     *
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return \DateTime The $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setCreatedAt($value)
    {
        $this->createdAt = $value;

        return $this;
    }

    /**
     * @return \DateTime The $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        $this->updatedAt = $value;

        return $this;
    }

    /**
     * @return boolean the $active
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     *
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return boolean The $verified
     */
    public function getVerified()
    {
        return $this->verified;
    }

    /**
     * @param boolean $verified
     *
     * @return $this
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * @return boolean the $shared
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * @param boolean $shared
     *
     * @return $this
     */
    public function setShared($shared)
    {
        $this->shared = $shared;

        return $this;
    }

    /**
     * @return string The $localeId
     */
    public function getLocaleId()
    {
        return $this->localeId;
    }

    /**
     * @param number $localeId
     *
     * @return $this
     */
    public function setLocaleId($localeId)
    {
        $this->localeId = $localeId;

        return $this;
    }

    /**
     * @return string The $timezone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     *
     * @return $this
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return \DateTime The $lastLoginAt
     */
    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
    }

    /**
     * @param \DateTime $lastLoginAt Time last logged in into Zendesk.
     *
     * @return $this
     */
    public function setLastLoginAt($lastLoginAt)
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    /**
     * @return string The $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string The $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return array The $identities
     */
    public function getIdentities()
    {
        return $this->identities;
    }

    /**
     * @param array $identities
     *
     * @return $this
     */
    public function setIdentities($identities)
    {
        $this->identities = $identities;

        return $this;
    }

    /**
     * @return string The $signature
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param string $signature
     *
     * @return $this
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @return string The $details
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param string $details
     *
     * @return $this
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * @return string The $notes
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     *
     * @return $this
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return Number the $organizationId
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @param number $organizationId
     *
     * return $this
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;

        return $this;
    }

    /**
     * @return string the $role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     *
     * return $this
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return number the $customRoleId
     */
    public function getCustomRoleId()
    {
        return $this->customRoleId;
    }

    /**
     * @param number $customRoleId
     *
     * @return $this
     */
    public function setCustomRoleId($customRoleId)
    {
        $this->customRoleId = $customRoleId;

        return $this;
    }

    /**
     * @return boolean the $moderator
     */
    public function getModerator()
    {
        return $this->moderator;
    }

    /**
     * @param boolean $moderator
     *
     * @return $this
     */
    public function setModerator($moderator)
    {
        $this->moderator = $moderator;

        return $this;
    }

    /**
     * @return string the $ticketRestriction
     */
    public function getTicketRestriction()
    {
        return $this->ticketRestriction;
    }

    /**
     * @param string $ticketRestriction
     *
     * @return $this
     */
    public function setTicketRestriction($ticketRestriction)
    {
        $this->ticketRestriction = $ticketRestriction;

        return $this;
    }

    /**
     * @return boolean the $onlyPrivateComments
     */
    public function getOnlyPrivateComments()
    {
        return $this->onlyPrivateComments;
    }

    /**
     * @param boolean $onlyPrivateComments
     *
     * @return $this
     */
    public function setOnlyPrivateComments($onlyPrivateComments)
    {
        $this->onlyPrivateComments = $onlyPrivateComments;

        return $this;
    }

    /**
     * @return array the $tags
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     *
     * @return $this
     */
    public function setTags($tags)
    {
        if (!is_array($tags)) {
            $this->tags = explode(' ', $tags);
        } else {
            $this->tags = $tags;
        }

        return $this;
    }

    /**
     * @return boolean The $suspended
     */
    public function getSuspended()
    {
        return $this->suspended;
    }

    /**
     * @param boolean $suspended
     *
     * @return $this
     */
    public function setSuspended($suspended)
    {
        $this->suspended = $suspended;

        return $this;
    }

    /**
     * @return array the $customFields
     */
    public function getCustomFields()
    {
        return $this->customFields;
    }

    /**
     * @param array $value
     *
     * @return $this
     */
    public function setCustomFields($value)
    {
        $this->tags = $value;

        return $this;
    }

}
