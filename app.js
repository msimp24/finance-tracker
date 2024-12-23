$(document).ready(function () {
  // Get the current path from the URL
  const currentPath = window.location.pathname

  // Match the current path with the href of each nav link
  $('.nav-list li a').each(function () {
    if ($(this).attr('href') === currentPath) {
      $(this).parent().addClass('active')
    }
  })

  /* -- income page -- */

  $('#income-close').click(() => {
    $('#income-modal').fadeOut()
  })

  $('#income-btn').click(() => {
    $('#income-modal').fadeIn()
    $('#income-source').val('')
    $('#income-amount').val('')
    $('#income-date').val('')
    $('#income-notes').val('')
  })

  // form validation for adding income
  $('.income-form').on('submit', (event) => {
    let isValid = true
    let source = $('#income-source').val().trim()
    let amount = $('#income-amount').val().trim()
    let date = $('#income-date').val().trim()
    let notes = $('#income-notes').val().trim()

    if (source === '') {
      $('#source-error').text('Source is a required field.')
      isValid = false
    } else {
      $('#source-error').text('')
    }
    if (amount === '') {
      $('#amount-error').text('Amount is a required field.')
      isValid = false
    } else if (isNaN(amount) || parseFloat(amount) <= 0) {
      $('#amount-error').text('Amount must be a positive number.')
      isValid = false
    } else {
      $('#amount-error').text('')
    }

    if (date === '') {
      $('#date-error').text('Date is a required field.')
      isValid = false
    } else {
      $('#date-error').text('')
    }
    if (notes === '') {
      $('#notes-error').text('Notes is a required field.')
      isValid = false
    } else {
      $('#notes-error').text('')
    }

    if (!isValid) {
      event.preventDefault()
    }
  })

  //edit income transactions

  $('.income-edit').click(function () {
    let id = $(this).attr('id')

    if (!id) {
      console.log('Id not found')
      return
    }

    $.ajax({
      type: 'POST',
      url: '/finance-tracker/api.php',
      data: { action: 'edit-income', id: id },
      cache: false,
      success: function (response) {
        try {
          let income = JSON.parse(response)
          $('#incomeId').val(income[0].income_id)
          $('#income-source').val(income[0].source)
          $('#income-amount').val(income[0].amount)
          $('#income-date').val(income[0].income_date)
          $('#income-notes').val(income[0].notes)

          $('.income-form').css('display', 'flex')
          $('#income-modal').fadeIn()
        } catch (err) {
          console.log(err)
        }
      },
    })
  })

  $('.income-delete').click(function () {
    let id = $(this).attr('id')

    if (!id) {
      console.log('Id not found')
      return
    }

    $.ajax({
      type: 'POST',
      url: '/finance-tracker/api.php',
      data: { action: 'delete-income', id: id },
      cache: false,
      success: function (response) {
        let result = JSON.parse(response)
        if (result.status === 'success') {
          location.reload()
        } else {
          console.error('Error:', result.message)
        }
      },
    })
  })

  //expense handling page

  $('#expense-close').click(() => {
    $('#expense-modal').fadeOut()
  })

  $('#expense-btn').click(() => {
    $('#expense-modal').fadeIn()
    $('#expense-source').val('')
    $('#expense-amount').val('')
    $('#expense-date').val('')
    $('#expense-notes').val('')
    $('#expense-category').val('')
  })

  //add expense form handling
  $('.expense-form').on('submit', (event) => {
    let isValid = true

    // Fetch and trim input values
    let expSource = $('#expense-source').val()?.trim()
    let expAmount = $('#expense-amount').val()?.trim()
    let expDate = $('#expense-date').val()?.trim()
    let expNotes = $('#expense-notes').val()?.trim()
    let expCategory = $('#expense-category').val()?.trim()

    // Clear previous errors
    $('.error-text').text('')

    // Validate Category
    if (!expCategory || expCategory.trim() === '') {
      $('#categoryExp-error').text('Category is a required field.')
      isValid = false
    } else {
      $('#categoryExp-error').text('')
    }

    // Validate Source
    if (expSource === '') {
      $('#sourceExp-error').text('Source is a required field.')
      isValid = false
    }

    // Validate Amount
    if (expAmount === '') {
      $('#amountExp-error').text('Amount is a required field.')
      isValid = false
    } else if (isNaN(expAmount) || parseFloat(expAmount) <= 0) {
      $('#amountExp-error').text('Amount must be a positive number.')
      isValid = false
    }

    // Validate Date
    if (expDate === '') {
      $('#dateExp-error').text('Date is a required field.')
      isValid = false
    }

    // Validate Notes
    if (expNotes === '') {
      $('#notesExp-error').text('Notes is a required field.')
      isValid = false
    }

    // Prevent form submission if validation fails
    if (!isValid) {
      event.preventDefault()
      console.log('Form submission prevented due to validation errors.')
    }
  })

  //editing expense ajax post request

  $('.expense-edit').click(function () {
    let id = $(this).attr('id')
    console.log(id)

    if (!id) {
      console.log('Id not found')
      return
    }

    $.ajax({
      type: 'POST',
      url: '/finance-tracker/api.php',
      data: { action: 'edit-expense', id: id },
      cache: false,
      success: function (response) {
        try {
          let expense = JSON.parse(response)
          $('#expenseId').val(expense[0].expense_id)
          $('#expense-source').val(expense[0].source)
          $('#expense-amount').val(expense[0].amount)
          $('#expense-date').val(expense[0].expense_date)
          $('#expense-notes').val(expense[0].notes)
          $('#expense-category').val(expense[0].category)

          $('.expense-form').css('display', 'flex')
          $('#expense-modal').fadeIn()
        } catch (err) {
          console.log(err)
        }
      },
    })
  })

  $('.expense-delete').click(function () {
    let id = $(this).attr('id')

    if (!id) {
      console.log('Id not found')
      return
    }

    $.ajax({
      type: 'POST',
      url: '/finance-tracker/api.php',
      data: { action: 'delete-expense', id: id },
      cache: false,
      success: function (response) {
        let result = JSON.parse(response)
        if (result.status === 'success') {
          location.reload()
        } else {
          console.error('Error:', result.message)
        }
      },
    })
  })

  //handle pots page

  //open add new pots modal
  $('#pots-btn').click(() => {
    $('#pots-modal').fadeIn()
  })

  $('#pots-close').click(() => {
    $('#pots-modal').fadeOut()
  })

  //form error handling

  $('.pots-form').on('submit', function (event) {
    let isValid = true

    let potsName = $('#pot-name').val().trim()
    let potTarget = $('#pot-target').val().trim()

    if (potsName === '') {
      isValid = false
      $('#pot-name-error').text('Pot must have a name')
    } else {
      $('#pot-name-error').text('')
    }

    if (potTarget === '') {
      $('#pot-target-error').text('Target is a required field.')
      isValid = false
    } else if (isNaN(potTarget) || parseFloat(potTarget) <= 0) {
      $('#pot-target-error').text('Target must be a positive number.')
      isValid = false
    }

    if (!isValid) {
      event.preventDefault()
      console.log('Form submission prevented due to validation errors.')
    }
  })

  //add money handler
  $('.add-money').on('click', function () {
    const potId = $(this).data('pot-id')
    const potName = $(this).data('pot-name')
    const target = parseFloat($(this).data('target'))
    const totalSaved = parseFloat($(this).data('total-saved'))

    let progressPercentage = Math.min((totalSaved / target) * 100)

    if (progressPercentage > 100) {
      progressPercentage = 100
    }

    $('#modal-pot-name').text(`Add money to ${potName}`)
    $('#add-money-progress-bar').css('width', `${progressPercentage}%`)
    $('#add-money-target').text(`Target: $${target}`)
    $('#add-money-saved').text(`Total Saved: $${totalSaved}`)
    $('#add-money-id').val(potId)

    $('#add-money-modal').fadeIn()
  })

  $('#add-money-close').click(function () {
    $('#add-money-modal').fadeOut()
  })

  //add money form handler
  $('.add-money-form').on('submit', function (event) {
    let isValid = true

    let addMoney = $('#add-money').val().trim()
    let currBalance = $('#curr-balance').val().trim()

    if (addMoney === '') {
      $('#add-money-error').text('This is a required field.')
      isValid = false
    } else if (isNaN(addMoney) || parseFloat(addMoney) <= 0) {
      $('#add-money-error').text('Add money must be a positive number.')
      isValid = false
    } else if (parseFloat(addMoney) > parseFloat(currBalance)) {
      $('#add-money-error').text('Not enough money in balance.')
      isValid = false
    }

    if (!isValid) {
      event.preventDefault()
    }
  })

  //open tooltip for pots

  $('.tooltip-trigger').on('click', function (event) {
    event.stopPropagation()
    $(this).siblings('.tooltip-content').css('display', 'flex').hide().fadeIn()
  })

  $(document).on('click', function () {
    $('.tooltip-content').fadeOut()
  })

  $('.tooltip-content').on('click', function (event) {
    event.stopPropagation()
  })
  $('.tooltip-content .tooltip-btn').on('click', function () {
    $(this).closest('.tooltip-content').fadeOut() // Hide parent tooltip
  })

  //edit pots
  $('.edit-pot').on('click', function () {
    let id = $(this).data('id')

    $.ajax({
      type: 'POST',
      url: '/finance-tracker/api.php',
      data: { action: 'edit-pot', id: id },
      cache: false,
      success: function (response) {
        try {
          let pot = JSON.parse(response)

          $('#potId').val(pot[0].pots_id)
          $('#pot-name').val(pot[0].name)
          $('#pot-target').val(pot[0].target)

          $('.pots-form').css('display', 'flex')
          $('#pots-modal').fadeIn()
        } catch (err) {
          console.log(err)
        }
      },
    })
  })

  //deletes pot from the database
  $('.delete-pot').on('click', function () {
    let id = $(this).data('id')

    $.ajax({
      type: 'POST',
      url: '/finance-tracker/api.php',
      data: { action: 'delete-pot', id: id },
      cache: false,
      success: function (response) {
        let result = JSON.parse(response)
        if (result.status === 'success') {
          location.reload()
        } else {
          console.error('Error:', result.message)
        }
      },
    })
  })

  //withdraw money from pots

  $('#withdraw-money').on('click', function () {
    $('#withdraw-money-modal').fadeIn()
  })

  $('#withdraw-money-close').on('click', function () {
    $('#withdraw-money-modal').fadeOut()
  })

  $('.withdraw-money').on('click', function () {
    const potId = $(this).data('pot-id')
    const potName = $(this).data('pot-name')
    const target = parseFloat($(this).data('target'))
    const totalSaved = parseFloat($(this).data('total-saved'))

    const progressPercentage = Math.min((totalSaved / target) * 100)
  })
})
