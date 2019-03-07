<!-- PROJECT LOGO -->
<br />
<p align="center">
  <a href="https://github.com/heloufir/simple-workflow">
    <img src="https://drive.google.com/uc?id=1bmiIFxsdjCs5q6Qd-xDlO67dvB8MwvJV&export=download" alt="Logo">
    <!-- IMAGE LICENSE: https://www.kisspng.com/png-workflow-automation-business-process-organization-2395138/ -->
  </a>

  <h3 align="center">Laravel <b>Simple Workflow</b></h3>

  <p align="center">
    A standard workflow system ready to use in 3 steps! <b>Install</b> > <b>Configure</b> > <b>Use</b>
    <br />
    <a href="https://github.com/heloufir/simple-workflow/wiki"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://github.com/heloufir/simple-workflow/issues">Report Bug</a>
    ·
    <a href="https://github.com/heloufir/simple-workflow/issues">Request Feature</a>
  </p>
</p>

<!-- TABLE OF CONTENTS -->
<h1 id="table-of-contents">Table of contents</h1>
<ul>
  <li><a href="#table-of-contents">Table of contents</a></li>
  <li>
    <a href="#about-the-project">About the project</a>
    <ul>
      <li><a href="#built-with">Built with</a></li>
    </ul>
  </li>
  <li>
    <a href="#getting-started">Getting started</a>
    <ul>
      <li><a href="#prerequisites">Prerequisites</a></li>
      <li><a href="#installation">Installation</a></li>
    </ul>
  </li>
  <li><a href="#usage">Usage</a></li>
  <li><a href="#contributing">Contributing</a></li>
  <li><a href="#license">License</a></li>
  <li><a href="#contact">Contact</a></li>
</ul>

<!-- ABOUT THE PROJECT -->
<h1 id="about-the-project">About the project</h1>
<p align="center">
  <img src="https://drive.google.com/uc?id=1MdpN0a4nSCC7f30NTkPGDXuSACK3suqX&export=download" alt="About the project">
</p>
<p align="left">
  You can find several complex workflow management systems, or even very easy systems that are incomplete. This project is a generic abstraction with the features that any project that must implement a workflow system must have.
  <br/>
  Why choose this project among others?
  <ul>
  <li>Quick installation, <b>2 commands only!!</b></li>
  <li>Two commands that facilitate the following tasks:
    <ul>
      <li>Implementation of the workflow system on any model</li>
      <li>And, the configuration of workflow transitions</li>
    </ul>
  </li>
  <li>Pre-defined REST APIs allowing to interact with the different models of the workflow system (data recovery, insertion, update and deletion)</li>
  <li>Paging utilities, adding specs to the model</li>
  <li>... and many other benefits....</li>
  </ul>
</p>

<!-- BUILT WITH -->
<h2 id="built-with">Built with</h2>
<p align="left">This project is carried out mainly with Laravel 5.8.*</p>

<!-- GETTING STARTED -->
<h1 id="getting-started">Getting starter</h1>
<p align="left">This section will guide you step by step to install configure the workflow system on your project</p>

<!-- PREREQUISITES -->
<h2 id="prerequisites">Prerequisites</h2>
<p align="left">
  Before you start using this workflow system, you need to have a Laravel 5.8.* project installed.
  <br/>
  Please refer to the <a href="https://laravel.com/docs/5.8">official Laravel documentation</a> for more information.
</p>

<!-- INSTALLATION -->
<h2 id="installation">Installation</h2>
<p align="left">First of all, install the package using the composer command:</p>

```
  composer require heloufir/simple-workflow
```

<p align="left">After that you need to publish the package, to have access to it's configuration file:</p>

```
  php artisan vendor:publish --provider=Heloufir\SimpleWorkflow\SimpleWorkflowServiceProvider
```

<p align="left">That's it, your are ready to use it. You see! I told you <b>2 commands only!!</b></p>

<!-- USAGE -->
<h1 id="usage">Usage</h1>
<p align="left">
  There are several features provided by this workflow system, please refer to the <a href="https://github.com/heloufir/simple-workflow/wiki">wiki</a> of this repository for more information on how to use this system and get an idea about it's different features.
</p>

<!-- CONTRIBUTING -->
<h1 id="contributing">Contributing</h1>
<p align="left">
  Contributions are what make the open source community such an amazing place to be learn, inspire, and create. Any contributions you make are greatly appreciated.
  <ol>
    <li>Fork the project</li>
    <li>
      Create your feature branch
      <pre>git checkout -b feature/AmazingFeature</pre>
    </li>
    <li>
      Commit your changes
      <pre>git commit -m 'Add some AmazingFeature'</pre>
    </li>
    <li>
      Push to the branch
      <pre>git push origin feature/AmazingFeature</pre>
    </li>
    <li>Open a pull request</li>
  </ol>
</p>

<!-- LICENSE -->
<h1 id="license">License</h1>
<p align="left">
  Distributed under the <b>MIT License</b>. See <a href="https://github.com/heloufir/simple-workflow/blob/master/LICENSE">LICENSE</a> for more information.
</p>

<!-- CONTACT -->
<h1 id="contact">Contact</h1>
<p align="left">
  EL OUFIR Hatim <a href="mailto:eloufirhatim@gmail.com">eloufirhatim@gmail.com</a>
  <br/>
  Project Link: <a href="https://github.com/heloufir/simple-workflow">https://github.com/heloufir/simple-workflow</a>
</p>
