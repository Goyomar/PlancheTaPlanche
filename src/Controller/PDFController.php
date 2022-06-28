<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Adresse;
use App\Entity\Commande;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PDFController extends AbstractController
{
    /**
     * @Route("/pdf/facture", name="pdf_facture")
     */
    public function index(ManagerRegistry $doctrine, Session $session, MailerInterface $mailer)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $dompdf->getOptions()->setChroot("/css/pdf.css");

        $commande = $doctrine->getRepository(Commande::class)->findCurrentOrder($this->getUser());
        $factu = $session->get('adresseFactu');
        $livraison = $session->get('adresseLivraison');
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('pdf/facture.html.twig', [
            'commande' => $commande,
            'factu' => $factu,
            'livraison' => $livraison
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();
        
        // In this case, we want to write the file in the public directory
        $publicDirectory = 'C:\laragon\www\PlancheTaPlanche/public/pdf/';
        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath =  $publicDirectory . 'facture'.$commande.'.pdf';
        
        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);

        $mail = (new Email())
            ->from(new Address('noreply@ptp.fr', 'ptp noreply'))
            ->to($this->getUser()->getEmail())
            ->subject('Commande n°'.$commande)
            ->text("Merci pour votre commande !")
            ->attachFromPath($pdfFilepath)
        ;
        $mailer->send($mail);
        
        // Send some text response
        return $this->redirectToRoute('thank_you');
    }

    /**
     * @Route("/pdf/{numero}", name="show_pdf_facture")
     */
    public function showPDF($numero)
    {
        $publicResourcesFolderPath = 'C:\laragon\www\PlancheTaPlanche/public/pdf/';
        return new BinaryFileResponse($publicResourcesFolderPath."facture".$numero.'.pdf');
    }
}