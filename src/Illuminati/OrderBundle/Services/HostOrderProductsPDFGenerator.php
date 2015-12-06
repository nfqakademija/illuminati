<?php

namespace Illuminati\OrderBundle\Services;

use fpdf\FPDF;
use Doctrine\ORM\EntityManagerInterface;
use Illuminati\OrderBundle\Entity\Host_order;

class HostOrderProductsPDFGenerator extends FPDF implements OrderProductsPDFGeneratorInterface
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * Generates PDF with hosted order's products and participants
     *
     * @param \Illuminati\OrderBundle\Entity\Host_Order $hostOrder
     */
    public function generate(Host_order $hostOrder)
    {
        $repo = $this->em
            ->getRepository('IlluminatiOrderBundle:Host_order');

        $hostedOrderHost = $hostOrder->getUsersId();

        $products = $repo
            ->findOrderedProducts($hostOrder->getId());

        $participants = $repo
            ->findUserOrders($hostOrder->getId());

        $overallPriceSum = 0;

        $this->AliasNbPages();

        $this->newOrderPage($hostOrder->getTitle(), "Ordered Products:");

        // Generating Product Table;
        if (!empty($products)) {
            // Column widths
            $w = [120, 25, 45];

            $this->Cell($w[0], 6, "Product", 1, 0);
            $this->Cell($w[1], 6, "Quantity", 1, 0, "R");
            $this->Cell($w[2], 6, "Price", 1, 0, "R");
            $this->Ln();

            foreach ($products as $product) {
                $this->Cell($w[0], 6, $product['title'], 'LR');
                $this->Cell($w[1], 6, $product['quantity'], 'LR', 0, "R");
                $this->Cell($w[2], 6, $product['sum']." ".$product['currency'], 'LR', 0, "R");
                $this->Ln();

                $overallPriceSum+= $product['sum'];
            }

            $this->Cell($w[0], 1, '', 'T');
            $this->Cell($w[1], 6, "Sum", 1, 0, "R");
            $this->Cell($w[2], 6, $overallPriceSum." ".$products[0]['currency'], 1, 0, "R");
        }

        //Generating particiapnts table;
        $this->newOrderPage($hostOrder->getTitle(), "Order Participants: ");

        if (!empty($participants)) {
            $counter = 1;
            $w = [10,155,25];

            $this->Cell($w[0], 6, "Nr.", 1, 0);
            $this->Cell($w[1], 6, "Participant", 1, 0);
            $this->Cell($w[2], 6, "Prepaid", 1, 0, "R");
            $this->Ln();

            foreach ($participants as $participant) {
                // We skip the host of the order in the list
                if ($participant->getUsersId() == $hostedOrderHost) {
                    continue;
                }

                $this->Cell($w[0], 6, "{$counter}.", "LR", 0, "C");
                $this->Cell(
                    $w[1],
                    6,
                    "{$participant->getUsersId()->getName()} ".
                    "{$participant->getUsersId()->getSurname()} ".
                    "( {$participant->getUsersId()->getEmail()} )",
                    "LR"
                );

                $prepaid = $participant->getPayed() == 0 ? "No" : "Yes";

                $this->Cell($w[2], 6, $prepaid, "LR", 0, "R");

                $this->Ln();
                $counter++;
            }

            $this->Cell(array_sum($w), 1, '', 'T');
        }
    }

    /**
     * Generates new pdf page with provided header and sub-header text
     * @param $orderTitle
     * @param $subHeader
     */
    public function newOrderPage($orderTitle, $subHeader)
    {
        $this->AddPage("P", "A4");

        $this->SetFont("Arial", "B", 20);
        $this->Cell(0, 10, $orderTitle." - Order Details", 0, 1);

        $this->SetFont("Arial", "", 14);
        $this->Cell(0, 10, $subHeader, 0, 1);
    }

    /**
     * Generates pdf's footer
     */
    public function footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-17);
        $this->SetFont('Arial', '', 8);
        // Page number
        $this->Cell(0, 6, 'Page '.$this->PageNo().'/{nb}', 0, 1, 'C');

        $this->Cell(0, 6, 'NFQ Academy | TEAM #5 (Illuminati)', 0, 1, 'C');
    }
}
