<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber\Actions;

use App\Crm\Application\Service\Invoice;
use App\Crm\Transport\Event\PageActionsEvent;

final class InvoiceSubscriber extends AbstractActionsSubscriber
{
    public static function getActionName(): string
    {
        return 'invoice';
    }

    public function onActions(PageActionsEvent $event): void
    {
        $payload = $event->getPayload();

        /** @var Invoice $invoice */
        $invoice = $payload['invoice'];

        if ($invoice->getId() === null) {
            return;
        }

        $allowCreate = $this->isGranted('create_invoice');
        $allowView = $this->isGranted('view_invoice');
        $allowCustomer = $this->isGranted('access', $invoice->getCustomer());

        if ($allowCustomer && $allowCreate) {
            $event->addEdit($this->path('admin_invoice_edit', [
                'id' => $invoice->getId(),
            ]));
        }

        if ($allowCustomer && $allowView) {
            $event->addAction('download', [
                'url' => $this->path('admin_invoice_download', [
                    'id' => $invoice->getId(),
                ]),
                'target' => '_blank',
            ]);
        }

        if ($event->countActions() > 0) {
            $event->addDivider();
        }

        if ($allowCustomer && $allowCreate) {
            if (!$invoice->isPending()) {
                $event->addAction('invoice.pending', [
                    'url' => $this->path('admin_invoice_status', [
                        'id' => $invoice->getId(),
                        'status' => 'pending',
                        'token' => $payload['token'],
                    ]),
                ]);
            } else {
                $event->addAction('invoice.paid', [
                    'url' => $this->path('admin_invoice_status', [
                        'id' => $invoice->getId(),
                        'status' => 'paid',
                        'token' => $payload['token'],
                    ]),
                    'class' => 'modal-ajax-form',
                ]);
            }
        }

        $allowDelete = $this->isGranted('delete_invoice');
        if (!$invoice->isCanceled()) {
            $id = $allowDelete ? 'invoice.cancel' : 'trash';
            $event->addAction($id, [
                'url' => $this->path('admin_invoice_status', [
                    'id' => $invoice->getId(),
                    'status' => 'canceled',
                    'token' => $payload['token'],
                ]),
                'title' => 'invoice.cancel',
                'translation_domain' => 'actions',
            ]);
        }

        if ($this->isGranted('delete_invoice')) {
            $event->addDivider();
            $event->addDelete($this->path('admin_invoice_delete', [
                'id' => $invoice->getId(),
                'token' => $payload['token'],
            ]), false);
        }
    }
}
