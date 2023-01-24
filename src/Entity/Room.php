<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>.
 **/

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoomRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Room
{
    use SekolikoEtablissementTrait;
    use EntityIdTrait;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $places;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isReserved;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reservation", mappedBy="room")
     */
    private $reservations;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\OneToMany(targetEntity=Fourniture::class, mappedBy="room")
     */
    private $fourniture;

    /**
     * Room constructor.
     */
    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->fourniture = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Room
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlaces(): ?string
    {
        return $this->places;
    }

    /**
     * @param string $places
     *
     * @return Room
     */
    public function setPlaces(string $places): self
    {
        $this->places = $places;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsReserved(): ?bool
    {
        return $this->isReserved;
    }

    /**
     * @param bool|null $isReserved
     *
     * @return Room
     */
    public function setIsReserved(?bool $isReserved): self
    {
        $this->isReserved = $isReserved;

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    /**
     * @param Reservation $reservation
     *
     * @return Room
     */
    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setRoom($this);
        }

        return $this;
    }

    /**
     * @param Reservation $reservation
     *
     * @return Room
     */
    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            // set the owning side to null (unless already changed)
            if ($reservation->getRoom() === $this) {
                $reservation->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @param $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return Collection<int, Fourniture>
     */
    public function getFourniture(): Collection
    {
        return $this->fourniture;
    }

    public function addFourniture(Fourniture $fourniture): self
    {
        if (!$this->fourniture->contains($fourniture)) {
            $this->fourniture[] = $fourniture;
            $fourniture->setRoom($this);
        }

        return $this;
    }

    public function removeFourniture(Fourniture $fourniture): self
    {
        if ($this->fourniture->removeElement($fourniture)) {
            // set the owning side to null (unless already changed)
            if ($fourniture->getRoom() === $this) {
                $fourniture->setRoom(null);
            }
        }

        return $this;
    }
}
