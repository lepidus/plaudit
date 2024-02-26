describe('Plaudit - Widget show', function () {
    it("Plaudit widget is shown in submission's page", function () {
        cy.visit('');

        cy.contains('a', 'The Signalling Theory Dividends').click();
        cy.get('.item.plaudit');
        cy.contains('Plaudit');
    });
});