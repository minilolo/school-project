<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>.
 **/

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Student
{
    use SekolikoEtablissementTrait;
    use EntityIdTrait;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="boolean",options={"default":0})
     */
    private $isRenvoie;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClassRoom", inversedBy="students")
     */
    private $classe;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $contact;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $contactParent;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $adresseParent;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $noteLibre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StudentNote", mappedBy="student")
     */
    private $studentNotes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ecolage", mappedBy="student", cascade={"persist","remove"})
     */
    private $ecolages;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $contactMaman;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $contactResponsable;

    /**
     * @ORM\OneToMany(targetEntity=PresenceEleve::class, mappedBy="Eleve")
     */
    private $presenceEleves;

    /**
     * Student constructor.
     */
    public function __construct()
    {
        $this->isRenvoie = false;
        $this->studentNotes = new ArrayCollection();
        $this->ecolages = new ArrayCollection();
        $this->presenceEleves = new ArrayCollection();
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     *
     * @return Student
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsRenvoie(): ?bool
    {
        return $this->isRenvoie;
    }

    /**
     * @param bool $isRenvoie
     *
     * @return Student
     */
    public function setIsRenvoie(bool $isRenvoie): self
    {
        $this->isRenvoie = $isRenvoie;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deletedAt;
    }

    /**
     * @param DateTimeInterface|null $deletedAt
     *
     * @return Student
     */
    public function setDeletedAt(?DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return ClassRoom|null
     */
    public function getClasse(): ?ClassRoom
    {
        return $this->classe;
    }

    /**
     * @param ClassRoom|null $classe
     *
     * @return Student
     */
    public function setClasse(?ClassRoom $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     *
     * @return Student
     */
    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContact(): ?string
    {
        return $this->contact;
    }

    /**
     * @param string|null $contact
     *
     * @return Student
     */
    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    /**
     * @param string|null $adresse
     *
     * @return Student
     */
    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContactParent(): ?string
    {
        return $this->contactParent;
    }

    /**
     * @param string|null $contactParent
     *
     * @return Student
     */
    public function setContactParent(?string $contactParent): self
    {
        $this->contactParent = $contactParent;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAdresseParent(): ?string
    {
        return $this->adresseParent;
    }

    /**
     * @param string|null $adresseParent
     *
     * @return Student
     */
    public function setAdresseParent(?string $adresseParent): self
    {
        $this->adresseParent = $adresseParent;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNoteLibre(): ?string
    {
        return $this->noteLibre;
    }

    /**
     * @param string|null $noteLibre
     *
     * @return Student
     */
    public function setNoteLibre(?string $noteLibre): self
    {
        $this->noteLibre = $noteLibre;

        return $this;
    }

    /**
     * @return Collection|StudentNote[]
     */
    public function getStudentNotes(): Collection
    {
        return $this->studentNotes;
    }

    /**
     * @param StudentNote $studentNote
     *
     * @return Student
     */
    public function addStudentNote(StudentNote $studentNote): self
    {
        if (!$this->studentNotes->contains($studentNote)) {
            $this->studentNotes[] = $studentNote;
            $studentNote->setStudent($this);
        }

        return $this;
    }

    /**
     * @param StudentNote $studentNote
     *
     * @return Student
     */
    public function removeStudentNote(StudentNote $studentNote): self
    {
        if ($this->studentNotes->contains($studentNote)) {
            $this->studentNotes->removeElement($studentNote);
            // set the owning side to null (unless already changed)
            if ($studentNote->getStudent() === $this) {
                $studentNote->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ecolage[]
     */
    public function getEcolages(): Collection
    {
        return $this->ecolages;
    }

    /**
     * @param Ecolage $ecolage
     *
     * @return $this
     */
    public function addEcolage(Ecolage $ecolage): self
    {
        if (!$this->ecolages->contains($ecolage)) {
            $this->ecolages[] = $ecolage;
            $ecolage->setStudent($this);
        }

        return $this;
    }

    /**
     * @param Ecolage $ecolage
     *
     * @return $this
     */
    public function removeEcolage(Ecolage $ecolage): self
    {
        if ($this->ecolages->contains($ecolage)) {
            $this->ecolages->removeElement($ecolage);
            // set the owning side to null (unless already changed)
            if ($ecolage->getStudent() === $this) {
                $ecolage->setStudent(null);
            }
        }

        return $this;
    }

    public function getContactMaman(): ?string
    {
        return $this->contactMaman;
    }

    public function setContactMaman(string $contactMaman): self
    {
        $this->contactMaman = $contactMaman;

        return $this;
    }

    public function getContactResponsable(): ?string
    {
        return $this->contactResponsable;
    }

    public function setContactResponsable(string $contactResponsable): self
    {
        $this->contactResponsable = $contactResponsable;

        return $this;
    }

    /**
     * @return Collection<int, PresenceEleve>
     */
    public function getPresenceEleves(): Collection
    {
        return $this->presenceEleves;
    }

    public function addPresenceElefe(PresenceEleve $presenceElefe): self
    {
        if (!$this->presenceEleves->contains($presenceElefe)) {
            $this->presenceEleves[] = $presenceElefe;
            $presenceElefe->setEleve($this);
        }

        return $this;
    }

    public function removePresenceElefe(PresenceEleve $presenceElefe): self
    {
        if ($this->presenceEleves->removeElement($presenceElefe)) {
            // set the owning side to null (unless already changed)
            if ($presenceElefe->getEleve() === $this) {
                $presenceElefe->setEleve(null);
            }
        }

        return $this;
    }
}
